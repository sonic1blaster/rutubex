rutubex
=======

PHP class for API rutube.ru. Actual is alpha v.0.5
*For use need installed CURL on your php.

Simple example of use this library.
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
    154466,                             //id in your CMS
    false                               //adult
);

//if request will success returned array of video with rutube id.
var_dump($rv);

//get info about uploaded video
$v = $r->getVideo($rv['video_id']);

// Change preview screen
$rtv = $r->editThumbnail(
    $rv['video_id'],        //Rutube video ID
    $_FILE['tmp_name']      //uploaded image
);

// Change preview screen
$rtv = $r->editThumbnail(
    $rv['video_id'],        //Rutube video ID
    $_FILE['tmp_name']      //uploaded image
);

// Remove video from Rutube *(Factly video change special status to deleted and video always be saved on rutube side, but users can not view this video)
$r->deleteVideo($rv['video_id']);

//Set time of publication
$tt = array(
    'YYYY' => '2014',
    'MM' => '12',
    'DD' => '07',
    'HH' => '04',
    'MI' => '30',
    'SS' => '00'
);
$rtv = $r->editPubDate(
    $rv['video_id'],        //Rutube video ID
    $tt['YYYY'].'-'.$tt['MM'].'-'.$tt['DD'].'T'.$tt['HH'].':'.$tt['MI'].':'.$tt['SS'] //Special time format 
);
?>
</pre>
<br/>
&lt;iframe src=&quot;&lt;?= $v['embed_url'] ?&gt;&quot;&gt;&lt;/iframe&gt;

