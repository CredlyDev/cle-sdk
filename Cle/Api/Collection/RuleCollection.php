<?php namespace Cle\Api\Collection;

use Cle\Rule;

class RuleCollection extends ApiCollection{

    /**
     * Sets the description.
     *
     * @param      string  $desc   The description
     */

    public function setDesc( $desc ){

        $this->desc = $desc;

        return $this;
    }

    /**
     * Gets the description.
     *
     * @return     string  The description.
     */

    public function getDesc(){

        return $this->desc;
    }

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

        if(!isset($item->id)){
            throw new InvalidItemException('Rule id not set');
        }

        if(!isset($item->requirements)){
            throw new InvalidItemException(sprintf('Rule id: %s is missing requirements', $item->id));
        }

        $requireParams = ['grade_object_id', 'operator', 'value'];

        foreach($item->requirements as $req){

            $req = (array) $req;

            if(count(array_filter(array_only($req, $requireParams))) < 3){

                throw new InvalidItemException('requirement requires grade_object_id, operator and value');
            }

            $operators = [ 
                'greaterThanOrEqualTo',
                'greaterThan',
                'equalTo',
                'lessThanOrEqualTo',
                'lessThan'
            ];

            if(!in_array($req['operator'], $operators)){
                throw new InvalidItemException(
                    sprintf('Invalid requirement type, must be [%s]', implode(",", $operators))
                );
            }
        }

        return true;
    }

    /**
     * Sets the badge identifier.
     *
     * @param      <type>  $badgeId  The badge identifier
     *
     * @return     self    ( description_of_the_return_value )
     */

    public function setBadgeId( $badgeId ){

        $this->badgeId = $badgeId;

        return $this;
    }

    /**
     * Gets the badge identifier.
     *
     * @return     <type>  The badge identifier.
     */

    public function getBadgeId(){

        return $this->badgeId;
    }

    /**
     * Adds a rule.
     */

    public function addRule( $name, $callback ){

        $rule = new Rule();

        $callback( $rule );

        $this->items[] = (object) [
            'id'   => 'new',
            'name' => trim($name),
            'requirements' => $rule->getRequirements()
        ];

        return $this;    
    }

    /**
     * Gets the rule.
     *
     * @param      <type>  $name   The name
     *
     * @return     <type>  The rule.
     */

    public function getRule( $name ){

        return $this->search(function($rule, $key) use($name){

            if($rule->name === trim($name) || $rule->id === $name){
                return $rule;
            }
        });
    }

    /**
     * { function_description }
     */

    public function fetchItems( array $params = [] ){

        $res = $this->getClient()->get('cle_rules',[
            'query' => [
                'badge_id'     => $this->getBadgeId()
            ]
        ]);

        $data = $res->data;

        $this->items = $data->rules;
        $this->setDesc( $data->desc );

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
                'rules'    => $this->items,
                'desc'     => $this->getDesc(),
                'badge_id' => $this->getBadgeId(),
            ]
        ];

        $res = $this->getClient()->post('cle_rules/update', $params);

        return $this->items = $res->data->rules;
    }

}