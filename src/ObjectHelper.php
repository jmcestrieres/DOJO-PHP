<?php

namespace App;


class ObjectHelper {

    public function hydrate($object, array $data, array $fields): void 
    {
        foreach($fields as $field){
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            $object->$method($data[$field]);
        }
    }
}