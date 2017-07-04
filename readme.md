# u-html-minifier

HTML compression tool which takes into account the idiosyncrasies of responsys programming code.


## Links

- **GIT**: <git@ec2-54-232-93-180.sa-east-1.compute.amazonaws.com:emailsfrontend-team/u-html-minifier.git>
- **URL**: http://52.67.28.91:9090/stage/tools/minifier/


## Documentation

### What this tool does?

- Compress all HTML code in a single line.
- Removes all tab characters.
- Remove all HTML comments and content, with some exceptions:
- RSYS function inside comments.
- IE conditional comment.
- Special comment, starting with two asteriscs. 
- Ex.:```<!--** Keep this comment! -->`` 
- Empty comments, since they are used to target Outlook. 
- Ex.: ```<!-- -->```
- Remove white spaces before and after the folowing tags: ```<html>```, ```<body>```, ```<head>```, ```<meta>```, ```<style>```, ```<table>```, ```<tr>```, and ```<td>```.
- Removes spaces between attributes in the tags, except in the <img> tag (RSYS breaks image attribute src there is no spaces before or after it).
- Convert multiple consecutive white spaces into a single white space.