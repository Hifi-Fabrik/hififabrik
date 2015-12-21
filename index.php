<?php
/**
 * Created by PhpStorm.
 * User: Rudi
 * Date: 09.10.2015
 * Time: 13:05
 */

SESSION_START();

$usergroup = $_SESSION["user"]["usergroup"];

/**
if (isset($_GET["logedin"])){
    var_dump($_SESSION["user"]);
    exit;
}
**/

///**
require_once __DIR__.'../../vendor/autoload.php';

use Silex\Application;

//class MyApplication extends Application
//{
//    use Application\TwigTrait;
//    use Application\SecurityTrait;
//    use Application\FormTrait;
//    use Application\UrlGeneratorTrait;
//    use Application\SwiftmailerTrait;
//    use Application\MonologTrait;
//    use Application\TranslationTrait;
//}

$app = new Silex\Application;
//$app =new MyApplication();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/templates',
    'cache' => false,
));

$mainmenue[] = ["href" => "a target=\"_blank\" href=http://www.hififabrik.de",
                "text" => "HiFi-Fabrik Shop",
                "bgcolor" => '#00A0A0'];
$mainmenue[] = ["href" => "a href=main.php?MKZ=order&UGP=" . $_SESSION["user"]["user_group"],
                "text" => "Bestellungen / Reservierungen",
                "bgcolor" => '#FFC0FF'];
$mainmenue[] = ["href" => "a href=main.php?MKZ=lager&UGP=" . $_SESSION["user"]["user_group"],
                "text" => "Lager und Waren verwalten",
                "bgcolor" => '#A0A000'];

$mainmenue[] = ["href" => "a href=main.php?MKZ=preissuche&UGP=" . $_SESSION["user"]["user_group"],
                "text" => "Preissuchmaschinen",
                "bgcolor" => '#20FF20'];

$mainmenue[] = ["href" => "a href=main.php?MKZ=admin&UGP=" . $_SESSION["user"]["user_group"],
                "text" => "Administration",
                "bgcolor" => '#C0FFFF'];

$arr = array('title' => "Hifi-Fabik intern",
    'main' => $mainmenue,
    'userinfo' => $userinfo,
);

if (isset($_GET["logedin"])) {
//    $userinfo = GetUserInfo();
    $app->get('/', function () use ($app, $arr) {
        return $app['twig']->render('main.html', $arr);
    });
} else {
    $users = array(rw, ar, jr, ab, ik, ps, re, jw, sg);
    $arr=array('tile' => "testtitel",
               'users' => $users,
        );
    $app->get('/', function () use ($app, $arr) {
        return $app['twig']->render('login_formular.html', $arr);
    });
}

$app->run();

// checkout point in magento
// /httpdocs/app/design/frontend/hififabrik/default/template/checkout/success_new.phtml
//
// änderung zum Reservierungszettel drucken !!!



/*
// include and register Twig auto-loader
require_once '/vendor/Twig/Autoloader.php';
Twig_Autoloader::register();
echo "register ok";
try {
// specify where to look for templates
$loader = new Twig_Loader_Filesystem('templates');
echo "Twig_Loader_Filesystem ok";
// initialize Twig environment
$twig = new Twig_Environment($loader);
echo "Twig_Environment ok";

// load template
$template = $twig->loadTemplate('index.html');
echo "loadTemplate ok";

// set template variables
// render template
echo $template->render(array(
'name' => 'Clark Kent',
'username' => 'ckent',
'password' => 'krypt0n1te',
));
echo "render ok";


} catch (Exception $e) {
die ('ERROR: ' . $e->getMessage());
}
echo "Hello World";
*/
?>

