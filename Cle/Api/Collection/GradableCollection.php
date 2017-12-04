<?php namespace Cle\Api\Collection;

class GradableCollection extends ApiCollection{

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

        $required = ['callback', 'name', 'extern_id'];

        if(!is_array($item)) $item = (array) $item;

        if(count(array_only($item, $required)) !== 3 ){
            throw new InvalidItemException('Gradable requires name, extern_id and callback');
        }

        $callbacks = ['letterGrade','points','complete','pass'];

        if(!in_array($item['callback'], $callbacks)){
            throw new InvalidItemException(
                sprintf(
                    'Gradable "%s"\'s callback must be one of [%s]', 
                    $item['name'], 
                    implode(',', $callbacks)
                )
            );
        }

        return true;
    }

    /**
     * { function_description }
     */

    public function fetchItems( array $params = [] ){

        $res = $this->getClient()->get('cle_gradable', [
            'query' => [
                'access_token' => $this->tokens['access_token'],
                'integration_account_id' => $this->tokens['integration_account_id']
            ]
        ]);

        $this->items = json_decode($res->getBody())->data;

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
            'query' => $this->tokens,
            'form_params'=> [
                'grades' => $this->items
            ]
        ];

        $res = $this->getClient()->post('cle_gradable/create', $params);

        return json_decode($res->getBody());
    }

}