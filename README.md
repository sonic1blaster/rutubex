rutubex
=======

PHP class for API rutube.ru. Now in progress.

Simple example of upload video in rutube.
<pre>
<?
include("rutubex.php");
$r = new rutubex('b1e28995fb913c1cd00007a453c610e8da0b1158');

//if you don't have token, use login/password
//$r = new rutubex(array('username' => 'example@ex.com', 'password' => 'qwerty12345'));

$rv = $r->addVideo(
    'http://yourdomain.com/video.mp4',  //url to video
    'Title video',                      //title
    'My first video description',       //description
    true,                               //hidden
    5,                                  //id category in rutube 1-13
    'Tv show name',                     //TV show name. Created automaticly on the rutube side by name
    '2014',                             //year of realize video
    1,                                  //season
    1,                                  //current series
    2,                                  //type of video
    154466                              //id in your CMS
);

//if request will success returned array of video with rutube id.
var_dump($rv);

//get info about uploaded video
$v = $r->getVideo($rv['video_id']);
?>
</pre>
<br/>
&lt;iframe src=&quot;&lt;?= $v['embed_url'] ?&gt;&quot;&gt;&lt;/iframe&gt;

