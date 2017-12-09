<?php namespace Cle\Api\Collection;


class CourseCollection extends ApiCollection{

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

        $required = ['course_id', 'name'];

        if(!is_array($item)) $item = (array) $item;

        if(count(array_only($item, $required)) !== 2 ){
            throw new InvalidItemException('CLE Course requires name and course_id');
        }

        return true;
    }

    /**
     * { function_description }
     */

    public function fetchItems( array $params = [] ){

        $res = $this->getClient()->get('cle_integrations');

        $this->items = $res->data;

        return $this;
    }

    /**
     * Adds a course.
     *
     * @param      string  $id     The identifier
     * @param      string  $name   The name
     */

    public function addCourse( $id, $name ){

        //check for existing course id
        $key = $this->search(function($item, $key) use ($id){
            return $item->course_id === $id;
        });

        $course = (object) [
            'course_id' => $id, 
            'name' => $name
        ];

        if($key)
            $this->items[$key] = $course;
        else
            $this->items[] = $course;

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
                'courses' => $this->items
            ]
        ];

        $res = $this->getClient()->post('cle_integrations/create', $params);

        $this->items = (array) $res->data;
    }
}