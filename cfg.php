<?php
$configFile = 'testpage.cfg';
include $configFile;

$tmp = $pageConfig["params"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addParams']) )
    {
        $item_template = [
            'url_cosmetic' => 'your_value_here',
            'exception_antibot' => 'your_value_here',
            'tokenButtonName' => 'your_value_here',
            'tokenButtonType' => 'your_value_here',
            'isMainRow' => 'your_value_here',
            'SendTokenWithError' => 'your_value_here',
            'tokenName' => 'your_value_here',
            'wait_lag' => 'your_value_here',
            'enable_redirectpulse' => 'your_value_here',
            'a' => 'your_value_here',
            'b' => 'your_value_here',
            'c' => 'your_value_here',
            'x' => 'your_value_here',
            'y' => 'your_value_here',
            'z' => 'your_value_here',
        ];
        
        $selected_file=$_REQUEST["selected_files"];

        $isExist = false;
        $pageConfig["params"] = $tmp;
        $temp_array = $pageConfig["params"];
        foreach($temp_array as $key => $value) {
            if ($key == $selected_file) {
                $isExist = true;
                array_push($pageConfig["params"][$key], $item_template);
            }
        }
        
         $tmp = $pageConfig["params"];

        $exchange_char = "\n";

        if (PHP_OS == 'WINNT') $exchange_char = "\r\n";

        $content = json_encode($pageConfig);
        $content = str_replace("{", "[".$exchange_char, $content);
        $content = str_replace("}", $exchange_char."]".$exchange_char, $content);
        $content = str_replace(":", "=>", $content);
        $content = str_replace("\"", "'", $content);
        $content = str_replace("',", "',".$exchange_char, $content);
        file_put_contents($configFile, "<?php".$exchange_char.$exchange_char."\$pageConfig=$content;".$exchange_char.$exchange_char."?>");
        
       

    }
    else if (isset($_POST['params_key']) && isset($_POST['row_number']) && isset($_POST['json_key']) )
    {
        $pageConfig["params"][$_POST['params_key']][intval($_POST['row_number'])][$_POST['json_key']] = $_POST['json_value'];

        $exchange_char = "\n";

        if (PHP_OS == 'WINNT') $exchange_char = "\r\n";

        $content = json_encode($pageConfig);
        $content = str_replace("{", "[".$exchange_char, $content);
        $content = str_replace("}", $exchange_char."]".$exchange_char, $content);
        $content = str_replace(":", "=>", $content);
        $content = str_replace("\"", "'", $content);
        $content = str_replace("',", "',".$exchange_char, $content);
        file_put_contents($configFile, "<?php".$exchange_char.$exchange_char."\$pageConfig=$content;".$exchange_char.$exchange_char."?>");

        $response = array('message' => 'Data updated successfully');
        echo json_encode($response);
        exit;

    }
    else {
        // Read the current configuration from the file
        $configContents = file_get_contents($configFile);

        // Update the siteParams array with the new values
        $updatedConfigContents = $configContents;
        foreach ($_POST as $key => $value) {
            $updatedConfigContents = preg_replace("/'{$key}'\s*=>\s*'[^']*'/", "'{$key}' => '{$value}'", $updatedConfigContents);
        }

        // Write the updated configuration back to the file
        file_put_contents($configFile, $updatedConfigContents);

        // Redirect to the same page to show the updated values
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

}
// Read the current configuration from the file
$configContents = file_get_contents($configFile);

// Extract the siteParams array contents
$siteParamsContents = '';
preg_match("/'siteParams'\s*=>\s*\[([^\]]+)\]/s", $configContents, $matches);
if (!empty($matches)) {
    $siteParamsContents = $matches[1];
}

// Parse the siteParams array as PHP code
$siteParams = eval("return [$siteParamsContents];");

// Echo the siteParams array with editable fields and buttons
echo "<h3>$configFile config</h3>\n";
echo "<form method='POST'>\n";
foreach ($siteParams as $key => $value) {
    echo ucfirst($key) . ": <input type='text' name='$key' value='$value'>";
    echo "<button type='submit' name='update' value='$key'>Update</button><br>";
}
echo "</form>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        .card {
            font-size: 0.6rem;
            width: 190px;
            margin-bottom: 1rem;
        }
        .card-header {
            font-size: 0.8rem;
            padding: 0.2rem;
            word-wrap: break-word;
        }
        .card-body {
            padding: 0.2rem;
        }
        .card-text {
            font-size: 0.7rem;
            margin-bottom: 0.2rem;
            word-wrap: break-word;
        }

        .card-columns {
            column-count: 3;
        }

        @media (max-width: 576px) {
            .card-columns {
                column-count: 1;
            }
        }
    </style>
    <title>Parameter Cards</title>
</head>
<body>
<div class="container">
    <div class="card-columns">
        <?php


        foreach($tmp as $key => $value) {
            //do something with your $key and $value;

            $ii_row = 0;
            foreach ($value as $item) {
                echo '<div class="card">';
                echo '<div class="card-header">' . $key . '</div>';
                echo '<div class="card-body">';

                foreach ($item as $item_key => $item_value) {
                    echo '<p class="card-text">';
                    echo $item_key . ': <input type="text" name="' . $item_key . '" value="' . $item_value . '" onchange="updateData(this, \''.$key.'\', '.$ii_row.', \''.$item_key.'\')">';
                    echo '</p>';
                }

                echo '</div>';
                echo '</div>';

                $ii_row++;
            }

        }

        if (count($tmp) <= 0) {
            echo '<div class="alert alert-danger">Error: Unable to extract configuration from the file.</div>';
        }
        ?>

    </div>

    <?php
    echo "<form method='POST'>\n";
    echo "Select a token:";
    ?>
    <select name="selected_files">
        <?php

        foreach ($pageConfig["files"] as $file) {
            echo "<option value=".$file.">".$file."</option>";
        }

        ?>
    </select>
    <?php
    echo "<button type='submit' name='addParams'>Add Param Token</button><br>";
    echo "</form>";

    ?>
</div>

<script>
    function updateData(element, params_key, row_number, json_key) {
        var inputValue = element.value;
      
        var xhttp = new XMLHttpRequest();

        // Define the callback function to handle the response
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
                // Use the response from the server here
                location.reload();
            }
        };

        // Prepare the request
        xhttp.open("POST", "<?php echo $_SERVER['PHP_SELF']; ?>", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        // Create the data to send
        var data = "params_key="+params_key+"&row_number="+row_number+"&json_key="+json_key+"&json_value="+inputValue;

        // Send the request with the data
        xhttp.send(data);

    }
</script>

</body>
</html>
