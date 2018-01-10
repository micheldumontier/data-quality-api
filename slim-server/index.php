<?php
/**
 * Data Quality API
 * @version 0.1.0
 */

require_once __DIR__ . '/vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;
define("DATADIR","data");
define("DATASETFILE",DATADIR."/datasets.yaml");
if(!file_exists(DATASETFILE)) {
    throw new Exception ("No datasets to retrieve");
}
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new Slim\Container($configuration);
$app = new Slim\App($c);
$c['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        return $c['response']->withStatus(500)
                             ->withHeader('Content-Type', 'text/html')
                             ->write($exception->getMessage());
    };
};


/**
 * GET getDatasets
 * Summary: Get a list of evaluated datasets
 * Notes: Obtain a list of evaluated datasets
 * Output-Formats: [application/yaml]
 */
$app->GET('/datasets', function($request, $response, $args) {
    $basePath = $request->getUri()->getBaseURL();
    $datasets = Yaml::parseFile(DATASETFILE);
    foreach($datasets['datasets'] AS $k => &$obj) {
        $obj['quality-assessment'] = $basePath."/qa?id=".$obj['id'];
    }
    $result = Yaml::dump($datasets,2);
    $response->withHeader('Content-Type', 'application/yaml');
    $response->write($result);
    return $response;
});

/**
 * GET getQualityAssessmentByDatasetID
 * Summary: Get quality assessment for a specific dataset
 * Notes: 
 * Output-Formats: [application/yaml
 */
$app->GET('/qa[/{name}]', function($request, $response, $args) {
    $found = false;
    if(isset($args['name'])) {
        $name = strtolower($args['name']);
        $found = true;
    } else {
        $id = $request->getQueryParam("id", $default = null);
        $datasets = Spyc::YAMLLoad(DATASETFILE);
        foreach($datasets['datasets'] AS $obj) {
            if(strtolower($obj["id"]) == strtolower($id)) {
                $name = strtolower($obj['name']);
                $found = true;
                break;
            }
        }
    }
    // now find the specific datafile
    if($found === false) {
        throw new Exception ("Could not find quality assessment for $name");
    }
    $file = DATADIR."/".$name.".yaml";
    if(!file_exists($file)) {
        throw new Exception ("Could not find quality assessment file for $name");
    }
    $result = file_get_contents($file);
    $response->write($result);
    return $response;
});

$app->run();