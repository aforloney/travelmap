
<?php
session_start();
?>

<!DOCTYPE html>
<meta charset="utf-8">
<body>
  <script type="text/javascript">var user_id = <? echo json_encode($_SESSION["user_id"]) ?>;</script>
  <script src="../src/d3.v3.min.js"></script>
  <script src="../src/topojson.v1.min.js"></script>
  <script src="../src/datamaps.all.min.js"></script>
  <script src="../src/exif.js"></script>
  <script src="../src/load-image.all.min.js"></script>
  <script src="https://code.jquery.com/jquery-2.2.4.js"></script>
  <script src="../src/index.js"></script>
  
  <div id="myContainer" style="display:block">
    <div id="msgContainer" style="display:inline-block; position: absolute; top: 25px; left: 25px;">
        <div id="banner"></div>
        <div id="msg"></div>
        <div id="errorMessage" style="color:red;font:weight"></div>
    </div>
    <div id="fileUploader" style="display:inline-block; position: absolute; top: 100px; left: 25px">
     <form id="myForm" action="pending.php" method="POST" enctype="multipart/form-data">
         <input type="file" id="uploadImg" name="upload" style="display:block"></input>
         <input type="hidden" id="OK" name="values" value="hi"></input>
         <div id="address_input" style="display:inline-block">
            <input id="address" type="text" style="width:300px;float:right"></input>
          </div>
            <div id="form_footer" style="display:block">
              <input type="submit"></input>
            </div>
      </form>
    </div>
    <div id="container" style="top: 25px; left: 450px; width: 500px; height: 500px; display:inline-block"></div>
    <div id="previewContainer" style="display:inline-block;position: absolute; top: 500px; left: 25px">
      <div>Image Preview</div>
      <div id ="preview" style="margin: 5px; width:800px"></div>
    </div>

  </div>  
</body>