<?php
echo $_POST['field2'];
?>
<html>
<body>
    <form name="myform" action="test.php" method="post">
        <input type="text" name="field1[]">
        <input type="text" name="field2[]">

        <input type="text" name="field1[]">
        <input type="text" name="field2[]">

        <input type="submit" value="Send">
    </form>
</body>
</html>