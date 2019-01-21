<?php

namespace XIVDB\Apps\Community;

use Intervention\Image\ImageManagerStatic as Image;
use Ramsey\Uuid\Uuid;

//
// Screenshots file trait
//
trait ScreenshotsFiles
{
	//
	// Upload an image
	//
    public function upload($request)
    {
        // make sure we have an account
        if (!$this->getUser()) {
            return [false, 'not logged in'];
        }

        // get post stuff
        $data = base64_decode(explode(",", $request->get('data'))[1]);
        $name = $request->get('name');
        $type = $request->get('type');
        $size = $request->get('size');

        // content stuff
        $cid = $request->get('cid');
        $uid = $request->get('uid');

        // validate ...
        if (!$cid || !$uid || !$data) {
            return [false, 'Empty submitted data'];
        }

        // get valid stuff
        $valid = [
            'maxwidth' => 7680,
            'maxheight' => 4320,
            'minwidth' => 50,
            'minheight'=> 50,
            'size' => (10 * (1024 * 1024)),
            'mime' => ['image/png', 'image/jpeg', 'image/gif'],
            'extensions' => ['png', 'jpg', 'jpeg', 'gif', 'bmp'],
            'mime2ext' => ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/gif' => 'gif']
        ];

        // get some infor about the image
        $imageInfo = getimagesizefromstring($data);
        $imageType = trim($imageInfo['mime']);
        $width = trim($imageInfo[0]);
        $height = trim($imageInfo[1]);
        $extension = isset($valid['mime2ext'][$imageType]) ? trim($valid['mime2ext'][$imageType]) : null;

        // check all possible errors
        if (!$data)
        {
            return [false, 'Image is corrupt, could not verify.'];
        }
        else if (!$name)
        {
            return [false, 'Image has no name, give it a name!'];
        }
        else if (!$type || !in_array($type, $valid['mime']))
        {
            return [false, 'Image does not have a valid mime type'];
        }
        else if ($size > $valid['size'])
        {
            return [false, 'Image was too large, it must be under 10mb'];
        }
        else if ($width > $valid['maxwidth'] || $width < $valid['minwidth'])
        {
            return [false, 'Image widths are invalid, it must be above '. $valid['minwidth'] .' and below '. $valid['maxwidth']];
        }
        else if ($height > $valid['maxheight'] || $height < $valid['minheight'])
        {
            return [false, 'Image heights are invalid, it must be above '. $valid['minheight'] .' and below '. $valid['maxheight']];
        }
        else if (!in_array($extension, $valid['extensions']))
        {
            return [false, 'Image extension is invalid, it must be either: PNG, JPG or GIF'];
        }
        else
        {
            // make image
            $img = Image::make($data);
            if (!$img) {
                return [false, 'Image is corrupt, could not verify.'];
            }

            // create filename
            $name = explode('-', Uuid::uuid4())[0] .'.jpg';

            // get content text
            $contentText = array_search($cid, \XIVDB\Apps\Content\ContentDB::$contentIds);
            if (!$contentText) {
                return [false, 'Content id is invalid, try again or contact support'];
            }

            // data to insert
            $data = [
                'uniq' => $uid,
                'content' => $cid,
                'member' => $this->getUser()->id,
                'filename' => $name,
            ];

            // setup qb to insert
            $this->dbs->QueryBuilder->insert(self::TABLE)->schema(array_keys($data))->values($data);
            $success = $this->dbs->execute();
            if (!$success) {
                return [false, 'Could not add image, try again or contact support'];
            }

            // folder
            $folder = str_ireplace(['{type}', '{id}'], [$contentText, $uid], ROOT_WEB . '/screenshots/{type}/{id}/');

            // make dir if it does not exist
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            // create filename
            $filename = str_ireplace('.png', '.jpg', $folder . $name);
            $filenameSmall = str_ireplace('.png', '.jpg', $folder . 'small_'.$name);

            // save big and small versions
            $img->save($filename, 75);
            $img->resize(null, 250, function ($constraint) {
                $constraint->aspectRatio();
            })->save($filenameSmall, 75);

            return [true];
        }
    }

    //
    // Resize an uploaded image
    //
    private function resizeImage($file, $width, $height, $output)
    {
        $proportional = true;
        $quality = 100;

        if ($height <= 0 && $width <= 0) {
            return false;
        }

        # Setting defaults and meta
        $info                         = getimagesize($file);
        $image                        = '';
        $final_width                  = 0;
        $final_height                 = 0;
        list($width_old, $height_old) = $info;

        # Calculating proportionality
        if ($proportional)
        {
            if ($width == 0)
            {
                $factor = $height/$height_old;
            }
            elseif ($height == 0)
            {
                $factor = $width/$width_old;
            }
            else
            {
                $factor = min( $width / $width_old, $height / $height_old );
            }

            $final_width  = round( $width_old * $factor );
            $final_height = round( $height_old * $factor );
        }
        else
        {
            $final_width = ( $width <= 0 ) ? $width_old : $width;
            $final_height = ( $height <= 0 ) ? $height_old : $height;
        }

        # Loading image to memory according to type
        switch ($info[2])
        {
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($file);
                break;

            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($file);
                break;

            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($file);
                break;

            default:
            return false;
        }


        # This is the resizing/resampling/transparency-preserving magic
        $image_resized = imagecreatetruecolor( $final_width, $final_height );
        if (($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG))
        {
            $transparency = imagecolortransparent($image);

            if ($transparency >= 0)
            {
                $transparent_color  = imagecolorsforindex($image, $trnprt_indx);
                $transparency       = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
            }
            elseif ($info[2] == IMAGETYPE_PNG)
            {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
            }
        }

        imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);

        # Preparing a method of providing result
        switch (strtolower($output))
        {
            case 'browser':
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = NULL;
                break;

            case 'file':
                $output = $file;
                break;

            case 'return':
                return $image_resized;
                break;

            default:
                break;
        }

        # Writing image according to type to the output destination
        switch ( $info[2] )
        {
            case IMAGETYPE_GIF:
                imagegif($image_resized, $output, $quality);
                break;

            case IMAGETYPE_JPEG:
                imagejpeg($image_resized, $output, $quality);
                break;

            case IMAGETYPE_PNG:
                imagepng($image_resized, $output);
                break;

            default: return false;
        }

        return true;
    }
}
