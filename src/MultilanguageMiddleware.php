<?php
namespace slim3_multilanguage;

use Slim\Http\Request;

/*
 *  Multilanguage Middleware
 */
class MultilanguageMiddleware {

    private $container;

    private $twig;

    public $requestLang = "";

    public $langAvailable;

    public $requestUrl;

    public $defaultLang;

    /**
     * MultilanguageMiddleware constructor.
     * @param array $config
     */
    public function __construct($config)
    {
        $defaultConfig = [
            'twig' => null,
            'langFolder' => "../lang/"
        ];
        //on constitue la nouvelle config en regroupant la config par défault et celle donné par l'utilisateur
        $config = array_merge($defaultConfig, $config);

        //on set les variables
        $this->langAvailable = $config['availableLang'];
        $this->defaultLang = $config['defaultLang'];
        $this->container = $config['container'];
        $this->twig = $config['twig'];
        $this->langFolder = $config['langFolder'];
    }

    /**
     * this fonction is call by slim3
     */
    public function __invoke($request, $response, $next)
    {
        //request lang : la langue voulue prit depuis l'url (ex: "fr")
        $this->requestLang = $this->getWantLangFromUrlPath($request);

        //request Url : l'url demandé
        $this->requestUrl = $request->getUri()->getPath();

        
        if ($this->requestUrl[0] == '/'){
            $this->requestUrl = substr($request->getUri()->getPath(), 1);
        }

        //si le format de l'url est bon
       
        if ($this->requestUrl == '/' || empty($this->requestUrl) || preg_match("/^[a-z]{2}$|^[a-z]{2}\//",
                $this->requestUrl, $matches, PREG_OFFSET_CAPTURE, 0)){
            //si la langue demandé dans l'url n'est pas vide et que elle existe on la charge
            if (!empty($this->requestLang) && $this->ifLangExist($this->requestLang) == true){

                //on instancie stail lang
                $STAILang = new STAILang($this->requestLang, $this->langFolder);

                //on récupère l'array de la langue
                $langArray = $STAILang->getFileAsArray();

                if (!empty($this->twig)){
                    //si twig voulue, on push dans les variables globales en tant que 'lang'
                    $this->twig->addGlobal('lang', $langArray);
                }

                //on set un nouveau container 'lang' qui retourne la chaine de caractère de la langue voulue (ex: "fr")
                $this->container['lang'] = function (){
                    return $this->requestLang;
                };
                //on enregistre le dictrionnaire dans le container "dictionary")
                $this->container['dictionary'] = function () use($langArray){
                    return $langArray;
                };

                return $next($request, $response);

            }
            else{

                //si langue non valide on essaye avec la langue du navigateur
                $browserLang = $this->getBrowserLang();
                if ($this->ifLangExist($browserLang)){

                    //si la langue du navigateur est connue on redirige sur l'url corespondant à celle ci
                    //TO DO: faire en sorte que les paramètre dans l'url ne soit pas effacé (ex : quand on tape example.org/page que ça nous redirique pas sur example.org/fr mais sur example.org/fr/page)
                    return $response->withRedirect($request->getUri()->getBasePath() . '/' . $browserLang);

                }else{

                    //si la langue du navigateur n'est pas disponible on redirique vers la langue par défault
                    //TO DO: faire en sorte que les paramètre dans l'url ne soit pas effacé (ex : quand on tape example.org/page que ça nous redirique pas sur example.org/fr mais sur example.org/fr/page)
                    return $response->withRedirect($request->getUri()->getBasePath() . '/' . $this->defaultLang);

                }
            }
        }else{
            return $next($request, $response);
        }
    }

    /**
     * ifLangExiste
     *  Retourne vrai si la chaine de caractère passé dans le paramètre 1 existe dans l'array $this->langAvailable
     * 
     */
    public function ifLangExist($lang){
        if (in_array($lang, $this->langAvailable)){
            return true;
        }else{
            return false;
        }
    }

    public function getWantLangFromUrlPath(Request $request){

        $url = $request->getUri()->getPath();
        if ($url[0] == '/'){
            $url = substr($url, 1);
        }
        return explode('/', $url)[0];
    }

    public function getBrowserLang(){
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
            return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }else{
            return $this->defaultLang;
        }

    }
}
