<?php namespace Cle\Api\Collection;

class StudentCollection extends ApiCollection{

    /**
     * Determines if valid.
     *
     * @param      <type>                $item   The item
     *
     * @throws     InvalidItemException  (description)
     *
     * @return     boolean               True if valid, False otherwise.
     */

    public function isValid( $item ){

        if(!is_array($item)) $item = (array) $item;

        $required = ['last', 'email', 'extern_id'];

        $props   = array_filter(array_only($item, $required));

        if(count($props) < 3){
            throw new InvalidItemException(
                sprintf('student requires properties [%s]', implode(',', $props))
            );
        }

        return true;
    }

    /**
     * { function_description }
     */

    public function fetchItems( array $params = [] ){

        $res = $this->getClient()->get('cle_students');

        $this->items = $res->data;

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      array   $params  The parameters
     *
     * @return     <type>  ( description_of_the_return_value )
     */

    public function save( array $params = [] ){

        //validate before saving to databse
        foreach($this->items as $item){
            $this->isValid($item);
        }

        $params = [
            'form_params'=> [
                'students' => $this->items
            ]
        ];

        $res = $this->getClient()->post('cle_students/create', $params);

        return $this->items = $res->data;
    }
}