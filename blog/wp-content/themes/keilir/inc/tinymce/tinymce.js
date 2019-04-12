(function() {  

    "use strict";
 
    tinymce.PluginManager.add( 'bluthcodes_location', function( editor, url ) {

        editor.addButton( 'bluthcodes', {
            type: 'listbox',
            text: 'Bluthcodes',
            icon: false,
            onselect: function(e){},
            values: [
                {
                    text: 'Columns', 
                    menu: [
                        {
                            text: 'Half',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[two_first]<br /><br />[/two_first][two_second]<br /><br />[/two_second]');  }
                        },
                        {
                            text: 'Two/One',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[two_one_first]<br /><br />[/two_one_first][two_one_second]<br /><br />[/two_one_second]');  }
                        },
                        {
                            text: 'One/Two',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[one_two_first]<br /><br />[/one_two_first][one_two_second]<br /><br />[/one_two_second]');  }
                        },
                        {
                            text: 'Three',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[three_first]<br /><br />[/three_first][three_second]<br /><br />[/three_second][three_third]<br /><br />[/three_third]');  }
                        },
                        {
                            text: 'Four',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[four_first]<br /><br />[/four_first][four_second]<br /><br />[/four_second][four_third]<br /><br />[/four_third][four_fourth]<br /><br />[/four_fourth]');  }
                        },
                        {
                            text: 'One/One/Two',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[one_one_two_first]<br /><br />[/one_one_two_first][one_one_two_second]<br /><br />[/one_one_two_second][one_one_two_third]<br /><br />[/one_one_two_third]');  }
                        },
                        {
                            text: 'Two/One/One',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[two_one_one_first]<br /><br />[/two_one_one_first][two_one_one_second]<br /><br />[/two_one_one_second][two_one_one_third]<br /><br />[/two_one_one_third]');  }
                        },
                        {
                            text: 'One/Two/One',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[one_two_one_first]<br /><br />[/one_two_one_first][one_two_one_second]<br /><br />[/one_two_one_second][one_two_one_third]<br /><br />[/one_two_one_third]');  }
                        },
                    ]
                },{
                    text: 'Dropcap', 
                    menu: [
                        {
                            text: 'Normal',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[dropcap]' + tinyMCE.activeEditor.selection.getContent() + '[/dropcap]');  }
                        },
                        {
                            text: 'With Background',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[dropcap background="yes"  bgcolor="#333333"]' + tinyMCE.activeEditor.selection.getContent() + '[/dropcap]'); }
                        },
                        {
                            text: 'Demo',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[dropcap background="yes" color="#333333" size="50px"]' + tinyMCE.activeEditor.selection.getContent() + '[/dropcap]'); }
                        }
                    ]
                },{
                    text: 'PullQuote', 
                    menu: [
                        {
                            text: 'Normal',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[pullquote]' + tinyMCE.activeEditor.selection.getContent() + '[/pullquote]');  }
                        },
                        {
                            text: 'Right Aligned',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[pullquote align="right"]' + tinyMCE.activeEditor.selection.getContent() + '[/pullquote]');  }
                        },
                        {
                            text: 'Left Aligned',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[pullquote align="left"]' + tinyMCE.activeEditor.selection.getContent() + '[/pullquote]');  }
                        }
                    ]
                },{
                    text: 'Alert', 
                    menu: [
                        {
                            text: 'Error',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert type="error" close="true"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        },
                        {
                            text: 'Success',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert type="success" close="true"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        },
                        {
                            text: 'Warning',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert type="warning" close="true"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        }
                    ]
                },{
                    text: 'Label', 
                    menu: [
                        {
                            text: 'Success',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label type="success"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Warning',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label type="warning"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Important',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label type="important"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Info',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label type="info"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Inverse',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label type="inverse"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        }
                    ]
                },{
                    text: 'Badge', 
                    menu: [
                        {
                            text: 'Success',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge type="success"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Warning',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge type="warning"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Important',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge type="important"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Info',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge type="info"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Inverse',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge type="inverse"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        }
                    ]
                },{
                    text: 'Well', 
                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[well]' + tinyMCE.activeEditor.selection.getContent() + '[/well]');  }
                },{
                    text: 'Button', 
                    menu: [
                        {
                            text: 'Default',
                            menu: [
                                {
                                    text: 'Default',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="default" size="default" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Large',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="default" size="large" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Small',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="default" size="small" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Mini',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="default" size="mini" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                }
                            ]                                    
                        },
                        {
                            text: 'Primary',
                            menu: [
                                {
                                    text: 'Default',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="primary" size="default" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Large',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="primary" size="large" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Small',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="primary" size="small" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Mini',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="primary" size="mini" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                }
                            ]   
                        },
                        {
                            text: 'Info',
                            menu: [
                                {
                                    text: 'Default',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="info" size="default" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Large',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="info" size="large" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Small',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="info" size="small" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Mini',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="info" size="mini" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                }
                            ]   
                        },
                        {
                            text: 'Success',
                            menu: [
                                {
                                    text: 'Default',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="success" size="default" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Large',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="success" size="large" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Small',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="success" size="small" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Mini',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="success" size="mini" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                }
                            ]   
                        },
                        {
                            text: 'Warning',
                            menu: [
                                {
                                    text: 'Default',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="warning" size="default" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Large',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="warning" size="large" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Small',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="warning" size="small" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Mini',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="warning" size="mini" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                }
                            ]   
                        },
                        {
                            text: 'Danger',
                            menu: [
                                {
                                    text: 'Default',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="danger" size="default" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Large',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="danger" size="large" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Small',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="danger" size="small" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Mini',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="danger" size="mini" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                }
                            ]   
                        },
                        {
                            text: 'Inverse',
                            menu: [
                                {
                                    text: 'Default',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="inverse" size="default" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Large',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="inverse" size="large" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Small',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="inverse" size="small" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                },
                                {
                                    text: 'Mini',
                                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://www.example.com" style="inverse" size="mini" block="true|false" target="_blank to open in a new window _self for same window" icon="check"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                                }
                            ]   
                        }
                    ]
                },{
                    text: 'Syntax', 
                    menu: [
                        {
                            text: 'HTML',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[syntax type="html"]' + tinyMCE.activeEditor.selection.getContent() + '[/syntax]');  }
                        },
                        {
                            text: 'PHP',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[syntax type="php"]' + tinyMCE.activeEditor.selection.getContent() + '[/syntax]');  }
                        },
                        {
                            text: 'JS',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[syntax type="js"]' + tinyMCE.activeEditor.selection.getContent() + '[/syntax]');  }
                        },
                        {
                            text: 'CSS',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[syntax type="css"]' + tinyMCE.activeEditor.selection.getContent() + '[/syntax]');  }
                        },
                    ]
                },{
                    text: 'Tooltip', 
                    menu: [
                        {
                            text: 'Hover',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[tooltip text="Tooltip Text goes here" trigger="hover"]' + tinyMCE.activeEditor.selection.getContent() + '[/tooltip]');  }
                        },
                        {
                            text: 'Click',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[tooltip text="Tooltip Text goes here" trigger="click"]' + tinyMCE.activeEditor.selection.getContent() + '[/tooltip]');  }
                        },
                    ]
                },{
                    text: 'Progress Bar', 
                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[progress length="50" color="#3bd2f8"]' + tinyMCE.activeEditor.selection.getContent() + '[/progress]');  }
                },{
                    text: 'Popover', 
                    menu: [
                        {
                            text: 'Top',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[popover text="Popover Text goes here" trigger="hover" placement="top"]' + tinyMCE.activeEditor.selection.getContent() + '[/popover]');  }
                        },
                        {
                            text: 'Bottom',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[popover text="Popover Text goes here" trigger="hover" placement="bottom"]' + tinyMCE.activeEditor.selection.getContent() + '[/popover]');  }
                        },
                        {
                            text: 'Left',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[popover text="Popover Text goes here" trigger="hover" placement="left"]' + tinyMCE.activeEditor.selection.getContent() + '[/popover]');  }
                        },
                        {
                            text: 'Right',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[popover text="Popover Text goes here" trigger="hover" placement="right"]' + tinyMCE.activeEditor.selection.getContent() + '[/popover]');  }
                        },
                        {
                            text: 'Hover',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[popover text="Popover Text goes here" trigger="hover"]' + tinyMCE.activeEditor.selection.getContent() + '[/popover]');  }
                        },
                        {
                            text: 'Click',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[popover text="Popover Text goes here" trigger="click"]' + tinyMCE.activeEditor.selection.getContent() + '[/popover]');  }
                        },
                    ]
                },{
                    text: 'Tabs', 
                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[tabs-header]<br />[tabs-header-group open="one" active="yes"] HEADER ITEM [/tabs-header-group]<br />[tabs-header-group open="two"] HEADER ITEM [/tabs-header-group]<br />[/tabs-header]<br /><br />[tabs-content]<br />[tabs-content-group id="one" active="yes"]' + tinyMCE.activeEditor.selection.getContent() + '[/tabs-content-group]<br />[tabs-content-group id="two"][/tabs-content-group]<br />[/tabs-content]');  }
                },{
                    text: 'Accordion', 
                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[accordion]<br />[accordion-group title="Title goes here"]Text goes here[/accordion-group]<br />[accordion-group title="Title goes here"]Text goes here[/accordion-group]<br />[accordion-group title="Title goes here"]Text goes here[/accordion-group]<br />[/accordion]');  }
                },{
                    text: 'Divider', 
                    menu: [
                        {
                            text: 'White',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="white"]');  }
                        },
                        {
                            text: 'Thin',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="thin"]');  }
                        },
                        {
                            text: 'Thick',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="thick"]');  }
                        },
                        {
                            text: 'Short',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="short"]');  }
                        },
                        {
                            text: 'Dotted',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="dotted"]');  }
                        },
                        {
                            text: 'Dashed',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="dashed"]');  }
                        },
                        {
                            text: 'Thin w/big spacing',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="thin" spacing="25"]');  }
                        },
                    ]
                },{
                    text: 'Blockquote', 
                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[blockquote source="Name of the soruce"]Blockquote text goes here' + tinyMCE.activeEditor.selection.getContent() + '[/blockquote]');  }
                },
            ]
        });
    });

})();