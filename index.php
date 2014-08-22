<?php

header('Content-Type: text/html;charset=utf-8');

$response = null;
$isJson   = false;


if (!empty($_POST)) {
    
    $address = $_POST['address'];
    
    $h = curl_init($_POST['address']);
    curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($h, CURLOPT_VERBOSE, 1);


    $body = $_POST['json_data'];
    if(isset($_POST['param_regular']) && $_POST['param_regular'] == 1) {
        $body = strlen($_POST['json_data']) ? json_decode($_POST['json_data'], true) : array();
        $body = http_build_query($body);
    } else {
        $header = array('Content-Type: application/json');
    }

    
    
    if('POST' == $_POST['method']) {
        curl_setopt($h, CURLOPT_POST, true);
        curl_setopt($h, CURLOPT_POSTFIELDS, $body);
    }
    else if('PUT' == $_POST['method']) {
        curl_setopt($h, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($h, CURLOPT_POSTFIELDS, $body);
    }
    else if('DELETE' == $_POST['method']) {
        $data = strlen($_POST['json_data']) ? json_decode($_POST['json_data']) : array();
        curl_setopt($h, CURLOPT_CUSTOMREQUEST, "DELETE");
        if(!empty($data)){
            curl_setopt($h, CURLOPT_URL, $address.'?'.http_build_query($data));    
        }
        
    } else if('GET' == $_POST['method']) {
        $data = strlen($_POST['json_data']) ? json_decode($_POST['json_data']) : array();
        if(!empty($data)){
            curl_setopt($h, CURLOPT_URL, $address.'?'.http_build_query($data));
        }
    }

    if(isset($_POST['header']) && strlen($_POST['header']) > 0) {
        $header = array_merge($header, explode(';', html_entity_decode($_POST['header'])));
    }
    if(sizeof($header)) {
        curl_setopt($h, CURLOPT_HTTPHEADER, $header );
    }


    // curl_setopt($h, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 



    $response = curl_exec($h);
    $info = curl_getinfo($h);
    $json = new stdClass();
    if (strpos($info['content_type'], 'application/json') !== false) {
        $isJson = true;
        $json   = json_decode($response);
    }
    
    curl_close($h);
}

if(isset($json->imagedata)) {
    
    $data = base64_decode($json->imagedata); 
    file_put_contents('temp/'.$json->filename, $data);
    chmod('temp/'.$json->filename, 0777);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/bootstrap.css" />
    </head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>JSON API-Client <small>plain api calls</small></h1>
        </div>
<form method="post" action="">
    <label>URI</label>
    <input class="input-block-level" type="text" name="address" value="<?php echo @$_POST['address'] ?>" /><br/>
    <label>HEADER</label>
    <input class="input-block-level" type="text" name="header" value="<?php echo htmlentities(@$_POST['header']) ?>" /><br/>
    
    <div class="control-group">
    <label>HTTP-Methode</label>   
        <div class="btn-group method-select" data-toggle="buttons-radio">
            <button type="button" class="btn btn-success" id="method-GET">GET</button>
            <button type="button" class="btn btn-warning" id="method-POST">POST</button>
            <button type="button" class="btn btn-warning" id="method-PUT">PUT</button>
            <button type="button" class="btn btn-danger" id="method-DELETE">DELETE</button>
        </div>
    </div>
    <select name="method" class="input-block-level" id="method-switch">
        <option value="GET" <?php echo @$_POST['method'] == 'GET' ? 'selected="selected"':'' ?>>GET</option>
        <option value="POST" <?php echo @$_POST['method'] == 'POST' ? 'selected="selected"':'' ?>>POST</option>
        <option value="PUT" <?php echo @$_POST['method'] == 'PUT' ? 'selected="selected"':'' ?>>PUT</option>
        <option value="DELETE" <?php echo @$_POST['method'] == 'DELETE' ? 'selected="selected"':'' ?>>DELETE</option>
    </select>
    
    <label>JSON-Parameter</label>
    <textarea class="input-block-level" name="json_data" rows="15" cols="50" style=" font-family: monospace; font-size: 12px;"><?php echo @$_POST['json_data'] ?></textarea><br/>
    <br/>
    <label><input type="checkbox" name="param_regular" value="1" <?php echo @$_POST['param_regular'] == '1' ? 'checked="checked"':'' ?> > Json als regulären Parameter-String senden</label>
    <button type="submit" class="btn btn-primary btn-large">Senden</button>
</form>

<hr/>
<?php if(isset($info)): ?>
<pre>
<?php print_r($info) ?> 
</pre>
<pre>
<?php if(!$isJson) echo $response; else print_r($json) ; ?>
</pre>
<?php endif; ?>

<?php if(isset($json->imagedata)): ?>
<img src="<?php echo 'temp/'.$json->filename ?>" />
<?php endif; ?>
</div>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script>
        $(document).ready(function(){
            var method = $('#method-switch').val(); 
            $('.method-select button').removeClass('active');
            $('#method-'+method).addClass('active');
            
            $('#method-switch').hide();
            
            $('.method-select button').click(function(e){
                var method = $(this).attr('id').split('-').pop();
                $('#method-switch').val(method);
            });
        });
    </script>
</body>
</html>





