<?php

namespace XIVDB\Apps\Site;

/*  Sanitize class
 *  - Cleans crappy user input :D
 */
class Sanitize
{
    
    private $value;
    private $type;
    
    //SETS VALUE INITIALLY
    public function set($value) {
        $this->value = trim($value);
        return $this;
    }
    
    //ENDING VALUE
    public function get() {
        return $this->value;
    }
    
    // Types:
    // 'text' (default), 'email', 'url',
    // 'numeric', 'float', 'bool', 'image'
    public function validate($type = 'text') {
        
        switch ($type) {
            
            //TEXT TYPE
            case 'text':
                
                //CHECK ARRAY
                if (is_array($this->value)) {
                    
                    foreach ($this->value as $key => $val) {
                        //CHECK IF A STRING
                        if (!is_string($val)) {
                            $this->value = FALSE;
                        }
                    }
                }
                else {
                    
                    //CHECK IF A SINGLE STRING
                    if (!is_string($this->value)) {
                        $this->value = FALSE;
                    }
                }
                
                break;
            
            //EMAIL TYPE
            case 'email':
                $this->value = filter_var($this->value, FILTER_VALIDATE_EMAIL);
                break;
            
            //URL WEB ADDRESS TYPE
            case 'url':
                $this->value = filter_var($this->value, FILTER_VALIDATE_URL);
                break;
            
            //NUMERIC NUMBER (whole)
            case 'numeric':
                //FOR MULTIPLE SELECTS OR CHECKBOXES
                if (is_array($this->value)) {
                    
                    foreach ($this->value as $key => $val) {
                        if (filter_var($this->value, FILTER_VALIDATE_INT) === FALSE) {
                            $this->value = FALSE;
                        }
                    }
                }
                else {
                    $this->value = filter_var($this->value, FILTER_VALIDATE_INT);
                }
                break;
            
            //NUMERIC NUMBER (decimal)
            case 'float':
                $this->value = filter_var($this->value, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                break;
            
            //TRUE/FALSE ONLY
            case 'bool':
                $this->value = filter_var($this->value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                break;
            
            //IMAGE (jpg/png/gif only)
            case 'image':
                
                $image = $this->value;
                
                $imageName  =  $image['name'];
                $imageType  =  $image['type'];
                $imageTmp   =  $image['tmp_name'];
                $imageError =  $image['error'];
                $imageSize  =  $image ['size'];
                
                //CHECK IF FILE UPLOAD WAS SUCCESSFUL
                if ($imageError != 4) {
                    $this->value = FALSE;
                    
                } else {
                    //GET IMAGE DETAILS
                    $imageDetails = getimagesize($fileInfo['tmp_name']);
                    $goodMIME = ['image/jpeg', 'image/gif', 'image/png'];
                    
                    //CHECK IF IMAGE AT ALL
                    if (!$imageDetails) {
                        $this->value = FALSE;
                        
                        //CHECK IF MIME IS ACCEPTED TYPE
                    } elseif (!in_array($imageDetails['mime'], $goodMIME)) {
                        $this->value = FALSE;
                        
                        //CHECK IF IMAGE HAS DEMENSIONS
                    } elseif ($imageDetails[0] < 0 && $imageDetails[1] < 0) {
                        $this->value = FALSE;
                    }
                }
                
                break;
        }
        
        //SET VARIABLES FOR CHAIN USE
        $this->type = $type;
        
        return $this;
    }
    
    //SANITIZE INPUTS
    public function sanitize() {
        
        switch ($this->type) {
            
            //TEXT TYPE
            case 'text':
                //CHECK ARRAY
                if (is_array($this->value)) {
                    foreach ($this->value as $key => $val) {
                        $this->value[] = filter_var($this->value, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                    
                }
                else {
                    $this->value = filter_var($this->value, FILTER_SANITIZE_SPECIAL_CHARS);
                }
                break;
            
            //EMAIL TYPE
            case 'email':
                $this->value = preg_replace( '((?:\n|\r|\t|%0A|%0D|%08|%09)+)i' , '', $this->value);
                $this->value = filter_var($this->value, FILTER_SANITIZE_EMAIL);
                break;
            
            //URL WEB ADDRESS TYPE
            case 'url':
                //ADD HTTP ONTO URL IF NECESSARY
                if (!strpos('http://', $this->value)) {
                    $this->value = 'http://' . $this->value;
                }
                
                $this->value = filter_var($this->value, FILTER_SANITIZE_URL);
                break;
            
            //NUMERIC NUMBER (whole)
            case 'numeric':
                //FOR MULTIPLE SELECTS OR CHECKBOXES
                if (is_array($this->value)) {
                    
                    foreach ($this->value as $key => $val) {
                        $this->value[] = filter_var($this->value, FILTER_SANITIZE_NUMBER_INT);
                    }
                }
                else {
                    $this->value = filter_var($this->value, FILTER_SANITIZE_NUMBER_INT);
                }
                break;
            
            //NUMERIC NUMBER (decimal)
            case 'float':
                $this->value = filter_var($this->value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                break;
            
            //TRUE/FALSE ONLY
            case 'bool':
                $this->value = filter_var($this->value, FILTER_SANITIZE_SPECIAL_CHARS);
                break;
            
            //IMAGE (jpg/png/gif only)
            case 'image':
                //nothing
                break;
        }
        
        return $this;
    }
    
    public function strip() {
        
        //STRIP OPEN/END WHITE SPACE, REMOVE HTML TAGS, ADD SLASHES TO QUOTES
        $this->value = trim($this->value);
        $this->value = filter_var($this->value, FILTER_SANITIZE_STRING);
        $this->value = filter_var($this->value, FILTER_SANITIZE_MAGIC_QUOTES);
        
        return $this;
    }
    
    public function length($min, $max) {
        
        //CHECK INPUT AGAINST THIS MIN AND MAX
        if ($this->type != 'image') {
            
            //TOO SHORT...
            if (strlen($this->value) < $min) {
                $this->value = FALSE;
            }
            
            //TOO LONG...
            elseif (strlen($this->value) >= $max) {
                $this->value = FALSE;
            }
            
        }
        
        return $this;
    }
    
    public function alphanum()
    {
        if (!ctype_alnum($this->value)) {
            $this->value = FALSE;
        }
        
        return $this;
    }
    
    public function lowercase()
    {
        $this->value = strtolower($this->value);
        return $this;
    }
    
    public function uppercase()
    {
        $this->value = strtoupper($this->value);
        return $this;
    }
    
    public function substring($length) {
        
        $this->value = substr($this->value, 0, $length);
        
        return $this;
    }
}
