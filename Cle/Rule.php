<?php namespace Cle;

class Rule {

    protected $requirements = [];

    public function addRequirement( $objectId, $operator, $value ){

        $this->requirements[] = (object) [
            'grade_object_id' => $objectId,
            'operator'        => $operator,
            'value'           => $value,
        ];

        return $this;
    }

    public function getRequirements(){

        return $this->requirements;
    }

}