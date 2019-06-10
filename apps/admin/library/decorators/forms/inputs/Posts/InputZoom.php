<?php
namespace apps\admin\library\decorators\forms\inputs\Posts;
use apps\admin\library\decorators\DecoratorInterface;
use Phalcon\Forms\Element;

/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 15/03/2019
 * Time: 11:25
 */
class InputZoom implements DecoratorInterface
{
    /**
     * @var Element
     */
    protected $element;

    public function __construct(Element $el)
    {
        $this->element = $el;
    }

    public function decorate(){
        $this->element->setAttribute('grid_class', 'col-xs-4');
        $this->element->setAttribute('id', 'zoom');
        $this->element->setAttribute('placeholder', 'Intero da 1 a 22');
        $this->element->setAttribute('min', 1);
        $this->element->setAttribute('max', 22);
        return $this->element;
    }

}