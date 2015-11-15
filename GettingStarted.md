  1. copy "type.php" into a folder near the files you want to reference it from.
  1. add the following snippet of code to the top of each of your documents which renders HTML:

```
<?php require_once("type.php");
$type = new Typography; ?>
```

  1. call $type->process($text); on each of the strings you would like processed, where $text is that string.