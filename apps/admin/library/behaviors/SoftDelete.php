<?php

namespace apps\admin\library\behaviors;

use Phalcon\Mvc\Model\Behavior;
use Phalcon\Mvc\Model\BehaviorInterface;
use Phalcon\Mvc\ModelInterface;

class SoftDelete extends Behavior implements BehaviorInterface
{
    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    public function notify($eventType, ModelInterface $model)
    {
        if (\Phalcon\Di::getDefault()->getSession()->get('disabled-soft-delete-clause')) {
            \Phalcon\Di::getDefault()->getSession()->remove('disabled-soft-delete-clause');
            return true;
        }
        if ($eventType == 'beforeDelete') {

            $options = $this->getOptions();

            $field = $options ['field'];
            $value = $options ['value'];

            $model->skipOperation(true);

            if ($model->{$field} === $value)
                return;

            $updateModel = clone $model;
            $updateModel->{$field} = $value;

            $updateModel->isSoftDelete = true;

            if (!$updateModel->save()) {
                foreach ($updateModel->getMessages() as $message) {
                    $model->appendMessage($message);
                }
                return;
            }

            $model->{$field} = $value;

            $model->afterDelete();

            $modelsManager = $model->getModelsManager();
            $hasManyRelations = $modelsManager->getHasMany($model);

            foreach ($hasManyRelations as $relation) {
                $alias = $relation->getOptions() ['alias'];
                if (strpos($alias, 'History'))
                    continue;
                $relatedModels = $model->{"get{$alias}"} ();
                foreach ($relatedModels as $relModel) {
                    $relModel->delete();
                }
            }
        }
    }
}