<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/6/12
 * Time: 下午3:56
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Http\Attribute;

use FastD\Http\File\Uploaded\Uploader;
use FastD\Http\File\UploadFile;

/**
 * Class FilesAttribute
 *
 * @package FastD\Http\Attribute
 */
class FilesAttribute extends Attribute
{
    /**
     * @param array $files
     */
    public function __construct(array $files = [])
    {
        parent::__construct([]);
        $this->initializeUploadFilesArray($files);
    }

    /**
     * @param array $files
     */
    private function initializeUploadFilesArray(array $files = [])
    {
        foreach ($files as $name => $file) {
            if (is_array($file['name'])) {
                foreach ($file['name'] as $key => $value) {
                    if (empty($value)) {
                        continue;
                    }
                    $this->parameters[$name][$key] = new UploadFile($file['name'][$key], $file['type'][$key], $file['tmp_name'][$key], $file['size'][$key], $file['error'][$key]);
                }
                continue;
            } else if (!empty($file['name'])) {
                $this->set($name, new UploadFile($file['name'], $file['type'], $file['tmp_name'], $file['size'], $file['error']));
            }
        }
    }

    /**
     * @param array $config
     * @return Uploader
     */
    public function getUploader(array $config = [])
    {
        return new Uploader($config, $this->getFiles());
    }

    /**
     * @return UploadFile[]
     */
    public function getFiles()
    {
        return $this->all();
    }

    /**
     * @param $name
     * @return UploadFile
     */
    public function getFile($name)
    {
        return $this->get($name);
    }
}