# Introduction #
After designing the new configurable magicquote system, i thought it
would need some form of article about howto use it, since it can be
so powerful.

# Details #
Basicly it can handle four different lengths of input.
The array is formated like so:
```

public $magicquote_pairs = array(string FIND => array REPLACE);
```

We will start with REPLACE.
It is an array of replacements for your FIND.
Obviouisly if you only replace one thing it needs only one element in the array.
If you replace three though, you will need to format it as so:
```

array(first replacement, second replacement, third replacement)
```

Now lets work with FIND.
Find is a normal string which can have 4 possible lengths.
Each is described in the below table
| FIND length | Acrtions performed |
|:------------|:-------------------|
| 1           | Will find any instance of FIND and replace it with REPLACE[0](0.md) |
| 2           | Will find any pair of chars in FIND (e.g. "") and replace it with REPLACE[0](0.md) for the first and REPLACE[0](0.md) for the second. Sample:```
"\"\"" => array("&8221;", "&8220");```(Replaces "this" with '&8221;this&8220;' which is fancy quotes in html). |
| 3           | Will find any pair of chars in FIND[1-2] (e.g. "") that are not inside FIND[0](0.md). Sample:```
"#\"\"" => array("&8221;", "&8220");```(Replaces "this" with '&8221;this&8220;'  but will do nothing # to "this" until # i use the hash symbol agian. |
| 4           | Will find any pair of chars in FIND[1-2] (e.g. "") that are not inside FIND[0](0.md) and FIND[4](4.md) (which are treated like open and close tags). Sample:```
"<\"\">" => array("&8221;", "&8220;");``` (Replaces "this" with '&8221;this&8220;'  but will do nothing < to "this" until > i use the close symbol. you can <open and close> but nothing inside will be replaced. |

Complex to understand, but powerfull when used. (MUCH faster than regexp).