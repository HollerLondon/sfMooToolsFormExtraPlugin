/*
---

name: MooEditable.CleanPaste

description: Extends MooEditable to insert text copied from other editors like word without all that messy style-information.

license: MIT-style license

authors:
- André Fiedler <kontakt@visualdrugs.net>

requires:
- MooEditable
- MooEditable.Selection
- More/Class.Refactor

usage:
  Add the following tags in your html
  <link rel="stylesheet" href="MooEditable.css">
  <script src="mootools.js"></script>
  <script src="MooEditable.js"></script>
  <script src="MooEditable.CleanPaste.js"></script>

  <script>
  window.addEvent('domready', function(){
    var mooeditable = $('textarea-1').mooEditable();
  });
  </script>

provides: [MooEditable.CleanPaste]

...
*/

(function(){
    
    MooEditable = Class.refactor(MooEditable, {
      
        // @FIXED: Removed because inferred by above and breaks MooEditable completely.
        // Extends: MooEditable,
        
        attach: function(){
            var ret = this.previous();
            this.doc.body.addListener('paste', this.cleanPaste.bind(this));
            return ret;
        },
        
        cleanPaste: function(e){
            var txtPastet = e.clipboardData && e.clipboardData.getData ?
                e.clipboardData.getData('text/html') : // Standard
                window.clipboardData && window.clipboardData.getData ?
                window.clipboardData.getData('Text') : // MS
                false;
            
            // @FIXED: If !MS and data is not html - try this (ie. pasting plain text)
            if ((!txtPastet || '' == txtPastet.trim()) && e.clipboardData && e.clipboardData.getData) {
              var txtPastet = e.clipboardData.getData('Text');
            }
            
            if(!!txtPastet) {
                this.selection.insertContent(this.cleanHtml(txtPastet));
                new Event(e).stop();
            }
            else { // no clipboard data available
                this.selection.insertContent('<span id="INSERTION_MARKER">&nbsp;</span>');
                this.txtMarked = this.doc.body.get('html');
                this.doc.body.set('html', '');
                this.replaceMarkerWithPastedText.delay(5, this);
            }
            return this;
        },
        
        replaceMarkerWithPastedText: function(){
            var txtPastet = this.doc.body.get('html');
            var txtPastetClean = this.cleanHtml(txtPastet);
            this.doc.body.set('html', this.txtMarked);
    		    var node = this.doc.body.getElementById('INSERTION_MARKER'); 
			      this.selection.selectNode(node);
            this.selection.insertContent(txtPastetClean);
            return this;
        },
        
        cleanHtml: function(html){
          
            // @FIXED: Safari pastes in styles with ' not " - fixed to not be borken in safari
            // @FIXED: Word pastes in Safari
          
            // remove body and html tag
            html = html.replace(/<html[^>]*?>(.*)/gim, "$1");
            html = html.replace(/<\/html>/gi, '');
            html = html.replace(/<body[^>]*?>(.*)/gi, "$1");
            html = html.replace(/<\/body>/gi, '');
          
            // remove style, meta and link tags
            html = html.replace(/<style[^>]*?>[\s\S]*?<\/style[^>]*>/gi, '');
            html = html.replace(/<(?:meta|link)[^>]*>\s*/gi, '');
            
            // remove XML elements and declarations
            html = html.replace(/<\\?\?xml[^>]*>/gi, '');
            
            // remove w: tags with contents.
            html = html.replace(/<w:[^>]*>[\s\S]*?<\/w:[^>]*>/gi, '');
            
            // remove tags with XML namespace declarations: <o:p><\/o:p>
            html = html.replace(/<o:p>\s*<\/o:p>/g, '');
            html = html.replace(/<o:p>[\s\S]*?<\/o:p>/g, '&nbsp;');
            html = html.replace(/<\/?\w+:[^>]*>/gi, '');
            
            // remove comments [SF BUG-1481861].
            html = html.replace(/<\!--[\s\S]*?-->/g, '');
            html = html.replace(/<\!\[[\s\S]*?\]>/g, '');
            
            // remove mso-xxx styles.
            html = html.replace(/\s*mso-[^:]+:[^;"']+;?/gi, '');
            
            // remove styles.
            html = html.replace(/<(\w[^>]*) style='([^\']*)'([^>]*)/gim, "<$1$3");
            html = html.replace(/<(\w[^>]*) style="([^\"]*)"([^>]*)/gim, "<$1$3");
            
            // remove margin styles.
            html = html.replace(/\s*margin: 0cm 0cm 0pt\s*;/gi, '');
            html = html.replace(/\s*margin: 0cm 0cm 0pt\s*"/gi, "\"");
            
            html = html.replace(/\s*text-indent: 0cm\s*;/gi, '');
            html = html.replace(/\s*text-indent: 0cm\s*"/gi, "\"");
            
            html = html.replace(/\s*text-align: [^\s;]+;?"/gi, "\"");
            
            html = html.replace(/\s*page-break-before: [^\s;]+;?"/gi, "\"");
            
            html = html.replace(/\s*font-variant: [^\s;]+;?"/gi, "\"");
            
            html = html.replace(/\s*tab-stops:[^;"']*;?/gi, '');
            html = html.replace(/\s*tab-stops:[^"']*/gi, '');
            
            // remove font face attributes.
            html = html.replace(/\s*face="[^"']*"/gi, '');
            html = html.replace(/\s*face=[^ >]*/gi, '');
            
            html = html.replace(/\s*font-family:[^;"']*;?/gi, '');
            html = html.replace(/\s*font-size:[^;"']*;?/gi, '');
            
            // remove class attributes
            html = html.replace(/<(\w[^>]*) class=([^ |>]*)([^>]*)/gi, "<$1$3");
            
            // remove empty styles.
            html = html.replace(/\s*style='\s*'/gi, '');
            html = html.replace(/\s*style="\s*"/gi, '');
            
            html = html.replace(/<span\s*[^>]*>\s*&nbsp;\s*<\/span>/gi, '&nbsp;');
            
            html = html.replace(/<span\s*[^>]*><\/span>/gi, '');
            
            // remove lang attributes
            html = html.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/gi, "<$1$3");
            
            html = html.replace(/<span([^>]*)>([\s\S]*?)<\/span>/gi, '$2');
            
            html = html.replace(/<font\s*>([\s\S]*?)<\/font>/gi, '$1');
            
            html = html.replace(/<(u|i|strike)>&nbsp;<\/\1>/gi, '&nbsp;');
            
            html = html.replace(/<h\d>\s*<\/h\d>/gi, '');
            
            // remove "display:none" tags.
            html = html.replace(/<(\w+)[^>]*\sstyle="[^"']*display\s?:\s?none[\s \S]*?<\/\1>/ig, '');
            
            // remove language tags
            html = html.replace(/<(\w[^>]*) language=([^ |>]*)([^>]*)/gi, "<$1$3");
            
            // remove onmouseover and onmouseout events (from MS word comments effect)
            html = html.replace(/<(\w[^>]*) onmouseover="([^\"']*)"([^>]*)/gi, "<$1$3");
            html = html.replace(/<(\w[^>]*) onmouseout="([^\"']*)"([^>]*)/gi, "<$1$3");
            
            // the original <Hn> tag send from word is something like this: <Hn style="margin-top:0px;margin-bottom:0px">
            html = html.replace(/<h(\d)([^>]*)>/gi, '<h$1>');
            
            // word likes to insert extra <font> tags, when using IE. (Wierd).
            html = html.replace(/<(h\d)><font[^>]*>([\s\S]*?)<\/font><\/\1>/gi, '<$1>$2<\/$1>');
            html = html.replace(/<(h\d)><em>([\s\S]*?)<\/em><\/\1>/gi, '<$1>$2<\/$1>');
            
            // i -> em, b -> strong
            html = html.replace(/<b\b[^>]*>(.*?)<\/b[^>]*>/gi, '<strong>$1</strong>')
            html = html.replace(/<i\b[^>]*>(.*?)<\/i[^>]*>/gi, '<em>$1</em>')
            
            // remove "bad" tags
            html = html.replace(/<\s+[^>]*>/gi, '');
            
            // remove empty tags (three times, just to be sure).
            // This also removes any empty anchor
            html = html.replace(/<([^\s>]+)(\s[^>]*)?>\s*<\/\1>/g, '');
            html = html.replace(/<([^\s>]+)(\s[^>]*)?>\s*<\/\1>/g, '');
            html = html.replace(/<([^\s>]+)(\s[^>]*)?>\s*<\/\1>/g, '');
            
            // Convert <p> to <br />
            if (!this.options.paragraphise) {
                html.replace(/<p>/gi, '<br />');
                html.replace(/<\/p>/gi, '');
            }
            
            // Make it valid xhtml
            html = html.replace('/<br>/gi', '<br />');
            
            // remove <br>'s that end a paragraph here.
            html = html.replace(/<br[^>]*><\/p>/gim, '</p>'); 
            
            return html.trim();
        }
    });
    
})();