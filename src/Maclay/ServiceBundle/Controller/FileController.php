<?php

namespace Maclay\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    public function uploadFileAction($file, $path, $import, $upload)
    {
        try 
        {
            if (!isset($file["error"]) || is_array($file["error"])){
                throw new \RuntimeException("Invalid parameters.");
            }

            switch($file["error"]) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new \RuntimeException("No file sent.");
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new \RuntimeException("Exceeded filesize limit.");
                default:
                    throw new \RuntimeException("Unknown errors.");
            }

            if ($file["size"] > 10000000){
                throw new \RuntimeException("Exceeded filesize limit.");
            }

            $filetypes = array();
            if($import === TRUE){
                $filetypes = array("csv" => "text/plain");
            }
            else if ($upload === TRUE) {
                $filetypes = array("jpg" => "image/jpeg", "pdf" => "application/pdf");
            }
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            if (false === $ext = array_search($finfo->file($file["tmp_name"]),$filetypes, true)){
                throw new \RuntimeException("Invalid file format");
            }
            $fileName = sprintf("%s.%s", rand() . sha1_file($file["tmp_name"]), $ext);
            if(!move_uploaded_file($file["tmp_name"], $path . $fileName)){
                throw new \RuntimeException("Failed to move uploaded file.");
            }
            return new Response(json_encode(array("error" => "", "fileName" => $fileName)));
        }
        catch (\Exception $ee){
            return new Response(json_encode(array("error" => $ee->getMessage(), "fileName" => "")));
        }
    }
}
