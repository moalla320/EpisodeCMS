App.add('widget', '*', {
    init: function() {
        $('.widget .add-menu-item')
            .focus(function(){
               $this = $(this);
               if (!$this.attr('default')) {
                   $this.attr('default', $this.val());
               }
               if ($this.val() == $this.attr('default')) {
                   $this.val('');
               }
            })
            .focusout(function(){
               $this = $(this);
               if ($this.val() == '') {
                   $this.val($this.attr('default'));
               }
            })
//            .enter(function() {
//                alert('Enter');
//            })
    }
});