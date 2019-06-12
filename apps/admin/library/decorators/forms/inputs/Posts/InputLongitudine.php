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
class InputLongitudine implements DecoratorInterface
{
    /**
     * @var Element
     */
    protected $element;

    public function __construct(Element $el)
    {
        $this->element = $el;
    }

    public function decorate()
    {
        $this->element->setAttribute('grid_class', 'col-xs-4');
        $this->element->setAttribute('id', 'lng');
        $this->element->setAttribute('placeholder', 'Formato google es: 12.343289447');
        return $this->element;
    }

}