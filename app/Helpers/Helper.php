<?php

if (! function_exists('responseCRUD')) {
    function responseCRUD($tipe){
        switch ($tipe) {
            case 'create':
                $response = 'Data created successfully.';
                break;
            case 'update':
                $response = 'Data has been updated.';
                break;
            case 'delete':
                $response = 'Data has been deleted.';
                break;
        }
        return $response;
    }
}