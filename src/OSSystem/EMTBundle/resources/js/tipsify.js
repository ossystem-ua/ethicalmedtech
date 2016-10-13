/*
Labelify plugin

This plugin allows you to add tips into near fields with blur/focus action

Author: Jean-Christophe Cuvelier <cybertotophe@gmail.com>
Licence: GPL
Version: 1

*/

(function($) {
 $.fn.tipsify = function(end) {
   /**
    If undefined, we should have an empty string
   */
   if(undefined === end)
   {
     end = '';
   }
   /**
    Iterate to all the blocks we want to labelify
   */   
   this.each(function(){
     $block = $(this); 
     /**
      Iterate to all the labels we want to suppress
     */
     $(this).find('span[class="tips"]').each(function(){
       name = $(this).attr('for');
       var $text = $(this).html() + end;
       $(this).remove();
       
       $block.find('input[name="'+name+'"],textarea[name="'+name+'"]').each(function(){
         
         var $this = $(this);
         
         // We do not want to erase preset values
         if('' === $this.val())
         {
          $this.val($text); 
         }
         
         // Focus/Blur actions
         $this.focus(function(e) {
           if ($.trim($this.val()) == $text) $this.val("");
         });
         $this.blur(function(e) {
           if ($.trim($this.val()) == "") $this.val($text);
         });
         
         // Submit cleaning
         $(this).closest('form').submit(function(){
           if ($.trim($this.val()) == $text) $this.val("");
         });       
       });
       
     });
   });
 };
 
})(jQuery);