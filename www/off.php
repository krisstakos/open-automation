<?php
	echo <<<EOT
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" /> 
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="css/foundation.css" />
	<link rel="stylesheet" href="css/ui_colors.css" />
	<link rel="stylesheet" href="icon_pack/foundation-icons.css" />
</head>
<body>
<div class="off-canvas-wrap" data-offcanvas>
  <div class="inner-wrap">

    <a class="left-off-canvas-toggle" href="#" >Menu</a>

    <!-- Off Canvas Menu -->
    <aside class="left-off-canvas-menu">
        <!-- whatever you want goes here -->
        <ul>
          <li><a href="#">Item 1</a></li>
        ...
        </ul>
    </aside>

    <!-- main content goes here -->
    <p>Set in the year 0 F.E. ("Foundation Era"), The Psychohistorians opens on Trantor, the capital of the 12,000-year-old Galactic Empire. Though the empire appears stable and powerful, it is slowly decaying in ways that parallel the decline of the Western Roman Empire. Hari Seldon, a mathematician and psychologist, has developed psychohistory, a new field of science and psychology that equates all possibilities in large societies to mathematics, allowing for the prediction of future events.</p>

  <!-- close the off-canvas menu -->
  <a class="exit-off-canvas"></a>

  </div>
</div>
  <script src="js/vendor/jquery.js"></script>
  <script src="js/foundation.min.js"></script>
  <script src="js/functionality.js"></script>
  <script>
    $(document).foundation();
  </script>
</body>
</html>
EOT;
?>