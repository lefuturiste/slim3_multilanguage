<?php
namespace slim3_multilanguage;

/**
 * STAILang Class
 *
 * This class allow you to translate your website !
 * HOW TO USE IT :
 * ```` $lang = new STAILang("fr_FR"); // fr_FR is for the example you can use everything you want !````
 * ```` echo $lang->translate("word"); // This will show the translated word, you cans use everything for "word"````
 * ```` if($lang->isPresent("word")){ [...] // This function will return you true or false (true mean the word is present in the Lang File)````
 * ```` $lang->add("word", "WoRd"); // With this function you can add a word and its translation in the Lang File ````
 *
 * @author STAN-TAb Corp.
 * @link https://stantabcorp.com
 * @version 1.0.0
 * @copyright STAN-TAb Corp. 2016 All rights reserved
 * @license https://stantabcorp.com/license
 * @package stan-tab-corp\STAILang
 */

class STAILang{

    /**
     * @var string $lang the page lang
     */
    private $lang;

    /**
     * @var string $folder the translaion folder
     */
    public $folder;

    /**
     * @var string $ext the translation file extention
     */
    private $ext = ".lang";

    const SEARCH = true;

    /**
     * The construct method of STAILang !
     *
     * @param String $lang - The Langage of the username TIP: use for french fr_FR, so you can know le country and the language !
     * @param String $folder - The translaion folder
     * @return void
     */
    public function __construct($lang, $folder){
        $this->folder = $folder;

        $this->lang = $lang;
        if(!file_exists($this->folder)){
            mkdir($this->folder);
        }
        if(!file_exists($this->folder . $this->lang . $this->ext)){
            file_put_contents($this->folder . $this->lang . $this->ext, json_encode(array("lang" => $this->lang)));
        }
    }

    /**
     * The translate method !
     *
     * @param String $word - The word to translate
     * @param arry An array of world to replace
     * @return String The word translated in the correct language
     */
    public function translate($word, $replace = false){
        $content = json_decode(file_get_contents($this->folder . $this->lang . $this->ext), true);
        if(isset($content[$word])){
            if($replace){
                $final = "";
                foreach($replace as $key => $value){
                    $final = str_replace($key, $value, $content[$word]);
                }
                return $final;
            }else{
                return $content[$word];
            }
        }else{
            throw new Exception("Word ({$word}) not found in Lang File ({$this->lang}{$this->ext}) !", 1);
        }
    }

    /**
     * The metohd to add a word
     *
     * @param String $word - The word to translate
     * @param String $translation - The translation of the word
     * @return void
     */
    public function add($word, $translation){
        $content = json_decode(file_get_contents($this->folder . $this->lang . $this->ext), true);
        $content[$word] = $translation;
        file_put_contents($this->folder . $this->lang . $this->ext, json_encode($content));
    }

    /**
     * This method allow you to check if a word is present in the file
     *
     * @param String $word - The word to check if present
     * @return Boolean true or false
     */
    public function isPresent($word){
        $content = json_decode(file_get_contents($this->folder . $this->lang . $this->ext), true);
        if(isset($content[$word])){
            return true;
        }else{
            return false;
        }
    }

    /**
     * This method allow you to get the content of the lang file in an array
     *
     * @param void
     * @return Array of all translation
     */
    public function getFileAsArray(){
        return json_decode(file_get_contents($this->folder . $this->lang . $this->ext), true);
    }

    /**
     * This method detect the user's brower language
     *
     * @param $_SERVER['HTTP_ACCEPT_LANGUAGE']
     * @param boolean If check is needed
     * @param array List of accepted languages
     * @param string Default language if search failed
     * @return string The user's language
     */
    public function detectLanguage($http, $check = false, $langs = false, $default = false){
        $lang = explode(',',$http);
        if($check){
            if(in_array($lang, $langs)){
                return $lang;
            }else{
                return $default;
            }
        }else{
            return $lang;
        }
    }

    public function setLang($lang){
        $this->lang = $lang;
    }

}
