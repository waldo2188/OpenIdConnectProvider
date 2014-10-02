(function ($) {

    $.fn.template = function (options) {
        
        var content = this.html();

        for(index in options) {
            content = content.replace('__' + index + '__', options[index]);
        }
        
        return content;
    };
    
}(jQuery));
