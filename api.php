<?php
/**
 * Created by PhpStorm.
 * User: janhb
 * Date: 24.08.2017
 * Time: 16:43
 */

include_once('start_session.php');

use PartDB\Category;
use PartDB\Database;
use PartDB\Device;
use PartDB\Footprint;
use PartDB\Log;
use PartDB\Manufacturer;
use PartDB\Storelocation;
use PartDB\Supplier;
use PartDB\User;

$app = new Slim\App();
$database           = new Database();
$log                = new Log($database);
$current_user       = new User($database, $current_user, $log, 1); // admin


/**
 * Creates a error message
 * @param \Slim\Http\Response $response The existing response object.
 * @param string|array $message The message of the error.
 * @param int $code The HTTP error code of the error.
 * @param Exception|array $exception An Exception that happened
 * @return \Slim\Http\Response The updated response Object
 */
function generateError($response, $message = "", $code = 500, $exception = null)
{
    if($message != "")
        $error = array("message" => $message);
    if($exception != null)
        $error = array("message" => $exception->getMessage());

    return $response->withJson(array("code" => $code, "errors" => array($error)), $code);
}

function generateTreeForClass($class, &$database, &$current_user, &$log, $params = null, $page = "", $key = "")
{
    $root_id = (isset($params['root_id']) && $params['root_id'] >= 0) ? $params['root_id'] : 0;
    $root  = new $class($database, $current_user, $log,   $root_id);
    if(isset($params['page']) && isset($params['parameter']))
    {
        return $root->build_bootstrap_tree($params['page'],$params['parameter']);
    }
    else
    {
        return $root->build_bootstrap_tree($page, $key);
    }
}


/********************************************************************
 * Category
 ********************************************************************/
$app->get("/1.0.0/categories/{cid}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    if($args['cid'] < 1)
        return generateError($response, "The id must be greater 0!", 400);
    try {
        $category = new Category($database, $current_user, $log, $args['cid']);
        return $response->withJson($category->get_API_array(true));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});


/********************************************************************
 * Storelocation
 ********************************************************************/

$app->get("/1.0.0/locations/{lid}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    if($args['lid'] < 1)
        return generateError($response, "The id must be greater 0!", 400);
    try {
        $loc = new Storelocation($database, $current_user, $log, $args['lid']);
        return $response->withJson($loc->get_API_array(true));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

/********************************************************************
 * Manufacturer
 ********************************************************************/

$app->get("/1.0.0/manufacturers/{id}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    if($args['id'] < 1)
        return generateError($response, "The id must be greater 0!", 400);
    try {
        $man = new Manufacturer($database, $current_user, $log, $args['id']);
        return $response->withJson($man->get_API_array(true));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

/********************************************************************
 * Suppliers
 ********************************************************************/

$app->get("/1.0.0/suppliers/{id}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    if($args['id'] < 1)
        return generateError($response, "The id must be greater 0!", 400);
    try {
        $sup = new Supplier($database, $current_user, $log, $args['id']);
        return $response->withJson($sup->get_API_array(true));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

/********************************************************************
 * Attachement Types
 ********************************************************************/

$app->get("/1.0.0/attachementtypes/{id}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    if($args['id'] < 1)
        return generateError($response, "The id must be greater 0!", 400);
    try {
        $at = new AttachementType($database, $current_user, $log, $args['id']);
        return $response->withJson($at->get_API_array(true));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

/********************************************************************
 * Footprints
 ********************************************************************/

$app->get("/1.0.0/footprints/{id}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    if($args['id'] < 1)
        return generateError($response, "The id must be greater 0!", 400);
    try {
        $foot = new Footprint($database, $current_user, $log, $args['id']);
        return $response->withJson($foot->get_API_array(true));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

/********************************************************************
 * Parts
 ********************************************************************/

$app->get("/1.0.0/parts", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    try {
        $parts = Part::get_all_parts($database, $current_user, $log);
        return $response->withJson(convert_APIModel_array($parts));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

$app->get("/1.0.0/parts/noprice", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    try {
        $parts = Part::get_noprice_parts($database, $current_user, $log);
        return $response->withJson(convert_APIModel_array($parts));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

$app->get("/1.0.0/parts/ordered", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    try {
        $parts = Part::get_order_parts($database, $current_user, $log);
        return $response->withJson(convert_APIModel_array($parts));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

$app->get("/1.0.0/parts/obsolete", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    try {
        $parts = Part::get_obsolete_parts($database, $current_user, $log);
        return $response->withJson(convert_APIModel_array($parts));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

$app->get("/1.0.0/parts/{id}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    if($args['id'] < 1)
        return generateError($response, "The id must be greater 0!", 400);
    try {
        $part = new Part($database, $current_user, $log, $args['id']);
        return $response->withJson($part->get_API_array(true));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

$app->get("/1.0.0/parts/by-category/{id}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    if($args['id'] < 1)
        return generateError($response, "The id must be greater 0!", 400);
    try {
        $recursive = (isset($args['recursive'])) ?  $args['recursive'] : false;
        $category = new Category($database, $current_user, $log, $args['id']);
        $parts = $category->get_parts($recursive);
        return $response->withJson(convert_APIModel_array($parts));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

$app->get("/1.0.0/parts/by-location/{id}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    if($args['id'] < 1)
        return generateError($response, "The id must be greater 0!", 400);
    try {
        $recursive = (isset($args['recursive'])) ?  $args['recursive'] : false;
        $location = new Storelocation($database, $current_user, $log, $args['id']);
        $parts = $location->get_parts($recursive);
        return $response->withJson(convert_APIModel_array($parts));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

$app->get("/1.0.0/parts/by-footprint/{id}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    if($args['id'] < 1)
        return generateError($response, "The id must be greater 0!", 400);
    try {
        $recursive = (isset($args['recursive'])) ?  $args['recursive'] : false;
        $footprint = new Footprint($database, $current_user, $log, $args['id']);
        $parts = $footprint->get_parts($recursive);
        return $response->withJson(convert_APIModel_array($parts));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

$app->get("/1.0.0/parts/by-manufacturer/{id}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    if($args['id'] < 1)
        return generateError($response, "The id must be greater 0!", 400);
    try {
        $recursive = (isset($args['recursive'])) ?  $args['recursive'] : false;
        $manufacturer = new Manufacturer($database, $current_user, $log, $args['id']);
        $parts = $manufacturer->get_parts($recursive);
        return $response->withJson(convert_APIModel_array($parts));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

$app->get("/1.0.0/parts/by-supplier/{id}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    if($args['id'] < 1)
        return generateError($response, "The id must be greater 0!", 400);
    try {
        $recursive = (isset($args['recursive'])) ?  $args['recursive'] : false;
        $supplier = new Supplier($database, $current_user, $log, $args['id']);
        $parts = $supplier->get_parts($recursive);
        return $response->withJson(convert_APIModel_array($parts));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

$app->get("/1.0.0/parts/by-keyword/{keyword}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    try {
        $keyword = trim($args['keyword']);
        $keyword = trim($keyword, '"');
        $parts = Part::search_parts($database, $current_user, $log, $keyword,
            sie($args['groupby'], ""), sie($args['name'], true), sie($args['description'], true),
            sie($args['comment'], false), sie($args['footprint'], false), sie($args['category'], false),
            sie($args['location'], false), sie($args['supplier'], false), sie($args['partnr'], false), sie($args['manufacturer'], false),
            false);
        return $response->withJson(convert_APIModel_array($parts));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

$app->get("/1.0.0/parts/by-regex/{keyword}", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    try {
        $keyword = trim($args['keyword']);
        $keyword = trim($keyword, '"');
        $parts = Part::search_parts($database, $current_user, $log, $keyword,
            sie($args['groupby'], ""), sie($args['name'], true), sie($args['description'], true),
            sie($args['comment'], false), sie($args['footprint'], false), sie($args['category'], false),
            sie($args['location'], false), sie($args['supplier'], false), sie($args['partnr'], false), sie($args['manufacturer'], false),
            true);
        return $response->withJson(convert_APIModel_array($parts));
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

/********************************************************************
 * System
 ********************************************************************/

/**
 * Get the system version
 */
$app->get("/1.0.0/system/info", function($request, $response, $args) {
    $ver_str = SystemVersion::get_installed_version()->as_string();
    $data = array("version" => $ver_str,
        "gitBranch" => get_git_branch_name(), "gitCommit" => get_git_commit_hash());
    return $response->withJson($data);
});


/********************************************************************
 * Trees
 ********************************************************************/

/**
 * Get the tree for categories
 */
$app->get("/1.0.0/tree/categories[/{root_id}]", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    try {
        $tree = generateTreeForClass(Category::class, $database, $current_user, $log,  $args, "show_category_parts.php", "cid");
        return $response->withJson($tree);
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

/**
 * Get the tree for categories
 */
$app->get("/1.0.0/tree/devices[/{root_id}]", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    try {
        $tree = generateTreeForClass(Device::class, $database, $current_user, $log,  $args, "show_device_parts.php", "cid");
        return $response->withJson($tree);
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

/**
 * Get the tree for categories
 */
$app->get("/1.0.0/tree/footprints[/{root_id}]", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    try {
        $tree = generateTreeForClass(Footprint::class, $database, $current_user, $log,  $args, "show_footprint_parts.php", "fid");
        return $response->withJson($tree);
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

/**
 * Get the tree for storelocation
 */
$app->get("/1.0.0/tree/locations[/{root_id}]", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    try {
        $tree = generateTreeForClass(Storelocation::class, $database, $current_user, $log,  $args, "show_location_parts.php", "fid");
        return $response->withJson($tree);
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

/**
 * Get the tree for manufacturer
 */
$app->get("/1.0.0/tree/manufacturers[/{root_id}]", function($request, $response, $args) use (&$database, &$log, &$current_user) {
    try {
        $tree = generateTreeForClass(Manufacturer::class, $database, $current_user, $log,  $args, "show_manufacturer_parts.php", "fid");
        return $response->withJson($tree);
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});

/**
 * Get the tree for tools
 */
$app->get("/1.0.0/tree/tools[/]", function($request, $response, $args){
    try {
        $tree = buildToolsTree($args);
        return $response->withJson($tree);
    }
    catch (Exception $ex)
    {
        return generateError($response, "", 500, $ex);
    }
});


$app->run();