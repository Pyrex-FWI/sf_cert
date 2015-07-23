/**
 * Created by christophep on 12/10/2014.
 */


function pl_init(){
    var allLinks;

    console.log(allLinks.length);
    console.log(allLinks);
    var i = 0;
    for (i; i < allLinks.length; i++) {
        allLinks[i].addEventListener("click", function(e){console.log(e.originalTarget);e.stopImmediatePropagation()();}, true);
    }
}
jQuery('document').ready(function(){
    jQuery("a.plr_ddp").click(function(){
        audio = ' <audio controls><source style="visibility: hidden;" src="'+$(this).attr('href') +'" type="audio/mpeg"></audio>';
        $(this).parent().append(audio);
        //Stop All
        jQuery.each( $('body').find('audio'), function( i, player ) {
            player.pause();
        });
        //Play new
        jQuery.each( $(this).parent().find('audio'), function( i, player ) {
            player.play();
        });
        return false;

    });
});