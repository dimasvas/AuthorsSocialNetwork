<?php

namespace AppBundle\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\RequestStack;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Description of AjaxUploadImageHandler
 *
 * @author dimas
 */
class AjaxUploadImageHandler extends UploadedFile
{
    private $request;
    
    private $user;
    
    private $config;
    
    private $extension = '.png';
    
    private $fileName;
    
    private $tempFile;
    
    private $newFile;
    
    private $currentFile;
    
    private $absoluteFilePath;

    /**
     * 
     * @param RequestStack $request_stack
     * @param TokenStorage $tokenStorage
     * @param type $config
     */
    public function __construct(RequestStack $request_stack, TokenStorage $tokenStorage,  $config)
    {
        $this->request = $request_stack->getCurrentRequest();
        $this->user = $tokenStorage->getToken()->getUser();
        $this->config = $config;
        $this->fileName = $this->generateFilename();
        $this->tempFile = $this->config['tmp'] . $this->fileName;
        
        $this->writeTmpFile($this->getFileFromRequest());
        
        parent::__construct($this->tempFile, $this->fileName);
        
    }
    
    public function processFileFromRequest($type)
    { 
        $this->setFilePaths($type);
        $this->writeNewFile($type);
    }
    
    public function setCurrentFile($file)
    {
        if($file) {
            $this->currentFile = $this->absoluteFilePath .$file;
        }
    }
    
    public function getNewFileName()
    {
        return $this->fileName;
    }
    
    public function getRelativeFile() 
    {
        $relative = explode('/web', $this->absoluteFilePath);
        $relative = end($relative);

        return $relative . $this->getNewFileName();
    }
    
    public function deleteCurrentFile()
    {
        if($this->currentFile) {
            @unlink($this->currentFile);

            $this->checkDeletion($this->currentFile);
        }
    }
    
    public function deleteTmpFile() 
    {
        @unlink($this->tempFile);
        
        $this->checkDeletion($this->tempFile);
    }
    
    public function deleteNewFile() 
    {
        @unlink($this->newFile);
        
        $this->checkDeletion($this->newFile);
    }
    
    private function checkDeletion($file)
    {
        //Write to logs
        if(file_exists($file)) {
            throw new \Exception("Cannot delete file  {$file}");
        }
    }
    
    private function setFilePaths($type) 
    {
        switch ($type) {
            case 'composition':
                $this->absoluteFilePath = $this->config['composition_img'];
                break;
            case 'profile':
                $this->absoluteFilePath = $this->config['user_img'];
                break;
            default:
                throw new \Exception("Wrong working type:  {$type}");
        }
    }
    
    private function writeNewFile()
    { 
        $this->newFile = $this->absoluteFilePath . $this->fileName;
        
        //TODO: change to move from parent
        if(!copy($this->tempFile, $this->newFile)) {
            
            $this->deleteTmpFile();
            
            throw new \Exception('Cant copy uploaded file.');
        }
        
        $this->deleteTmpFile();
        
    }
    
    private function getFileFromRequest()
    {
        $string = $this->request->request->get('imagebase64');
                
        if(!$string){
            throw new \Exception('No image data.');
        }
        
        $parts = explode(',', $string );
        
        if(!array_key_exists(1, $parts)){
            throw new Exception('Corrupted file data.');
        }
        
        $data = base64_decode($parts[1]);
        
        return $data;
    }
    
    private function writeTmpFile($data) 
    {
        if(!file_put_contents($this->tempFile, $data)){
             throw new \Exception('Can\'t write temporary file');
        }
    }
    
    private function generateFilename()
    {
        return $this->user->getId() . '_'. uniqid() . $this->extension;
    }
}
