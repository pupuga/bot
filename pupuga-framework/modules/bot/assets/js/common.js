;var pupugaBot = {
    
    botSelector: '.pupuga-bot',
    botTimerMessageInterval: 1500,
    templateMessageBot: urlBot + 'templates/message-bot.mst',
    templateMessageClient: urlBot + 'templates/message-client.mst',
    templateAnswer: urlBot + 'templates/',
    data: dataBot,
    config: configBot,
    
    checkFilters: function (object) {
        var returnFilter = true,
            editField = object.siblings('[type="text"], [type="number"]');

        console.log ('editField');
        
        if (editField !== false && editField.val() === '') {
            editField.addClass('warning');
            returnFilter = false;
        }
        
        return returnFilter;
    },
    
    replaceShortCode: function (messageString, object) {
        var objectParent = jQuery(object).parent();
        var replaceCode = {
            'value' : objectParent.find('.value').val(),
            'units' : objectParent.find('.units').text()
        };
        
        for (var prop in replaceCode) (
            messageString = messageString.replace('[' + prop + ']', replaceCode[prop])
        );
        
        return messageString; 
    },
    
    publicBlock: function (itemObject, scroll) {
        var self = this;
        itemObject.removeClass('display-none');
        /**
         *  messages block scrolls
         */
        if (scroll) {
            self.scrollMessages();
        }
    },
    
    progressToComplete: function (itemObject) {
        var self = this;
        itemObject.find('.progress').remove();
        self.publicBlock(itemObject.find('.complete'), true);
    },

    scrollMessages: function () {
        var self = this;
        var top = jQuery(self.botSelector + ' .messages').outerHeight();
        jQuery(self.botSelector + ' .messages-wrapper').stop().animate({ scrollTop: top }, 0);
    },
    
    getMessagesClient: function (objectAnswer) {
        var self = this, 
            templateName = 'templateMessageClient',
            messageString = objectAnswer.data('response');
        if (messageString == '' || messageString == undefined) {
            messageString = objectAnswer.text();
        } else {
            messageString = self.replaceShortCode(messageString, objectAnswer);   
        }
        if (self.checkFilters(objectAnswer)) {
            jQuery(self.botSelector + ' .answers').html('');
            var json = {
                'client': {
                    'message': messageString
                }
            };
            jQuery.get(this[templateName], function(template) {
                Object.keys(self.config).forEach(function (key) {
                    json[key] = self.config[key];
                });
                var rendered = Mustache.render(template, json);
                jQuery(self.botSelector + ' .messages').append(rendered);
                self.scrollMessages();
                /**
                 *  get new message
                 */
                var objectAnswerParent = objectAnswer.parent(),
                    index = (objectAnswerParent.find('.button').data('index') !== undefined) 
                        ? objectAnswerParent.find('.button').data('index')
                        : '';
                /**
                 * condition 
                 */
                var target = objectAnswer.data('target'),
                    value = objectAnswerParent.find('[name="value"]').val();
                if (objectAnswer.data('conditional-min-target') !== undefined && objectAnswer.data('conditional-min-target') !== '' && 
                    objectAnswer.data('conditional-min-value') !== undefined && objectAnswer.data('conditional-min-value') !== '' &&
                    objectAnswer.data('conditional-min-value') > value) {
                    target = objectAnswer.data('conditional-min-target');
                }
                self.getBlock (
                    target, 
                    value,  
                    objectAnswerParent.find('.units').text(), 
                    objectAnswerParent.find('input.text').data('name'), 
                    index
                );
            });   
        }
        
    },
    
    actionAnswer: function () {
        var self = this;
        
        jQuery(this.botSelector + ' .answer .number').off('input').on('input', function () {
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
           /* if (jQuery(this).val() > jQuery(this).data('filter-max')) {
                jQuery(this).val(jQuery(this).val().replace(/[^.]/, ''));
            }*/
        });
        
        jQuery(this.botSelector + ' .answer .button').off('click').on('click', function () {
            if (jQuery(this).data('target') !== undefined && jQuery(this).data('target') !== ''){
                self.getMessagesClient(jQuery(this));
            }
        });

        jQuery(this.botSelector + ' .answer .text').off('keypress').on('keypress', function (evt) {
            var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
            if(keyCode == 13){
                jQuery(this).parent().find('button').click();
            }    
        });
        
    },
    
    getAnswers: function () {
        var self = this;
        var renderedHtml = [];
        self.data.answer.forEach(function(element, index, array) {
           jQuery.get(self.templateAnswer + 'field-' + element.type + '.mst' , function(template) {
               Object.keys(self.config).forEach(function (key) {
                   element[key] = self.config[key];
               });
               renderedHtml[index] = Mustache.render(template, element);
               if (array.length === index + 1) {
                   renderedHtml.forEach(function(element) {
                       var answersBlock = jQuery(self.botSelector + ' .answers');
                       answersBlock.append(element);
                   });
                   self.actionAnswer();        
               }
           });
           
        });
    },
    
    getBlock: function(target, value, units, name, index) {
        var self = this;
        jQuery.ajax({
            url: $ajax.url,
            type: "POST",
            data: {
                nonce: $ajax.nonce,
                action: 'getMessageBlock',
                target: target,
                value: value,
                name: name,
                units: units,
                language: langCurrent,
                index: index
            },
            success: function (dataBlock) {
                if (dataBlock !== '') {
                    self.data = jQuery.parseJSON(dataBlock);
                }
                self.getMessagesBot();
            }
        });
    },
    
    getMessagesBot: function () {
        var self = this,
            templateName = 'templateMessageBot';
        jQuery.get(this[templateName], function(template) {
            Object.keys(self.config).forEach(function (key) {
                self.data[key] = self.config[key];
            });
            var rendered = Mustache.render(template, self.data);
            jQuery(self.botSelector + ' .messages').append(rendered);
            var messages = jQuery(self.botSelector + ' .messages .message.display-none');
            var messagesLength = messages.length;
            if (messagesLength > 0) {
                var botTimerMessage = 0;
                messages.each(function () {
                    var jQuerySelf = jQuery(this);
                    if (botTimerMessage > 0) {
                        setTimeout(
                            /**
                             * progress process
                             **/
                            function () {
                                self.publicBlock(jQuerySelf, true);
                            }, botTimerMessage
                        );
                    } else {
                        self.publicBlock(jQuerySelf, true);
                    }
                    botTimerMessage = botTimerMessage + self.botTimerMessageInterval;
                    /**
                     * progress process to complete process
                     **/
                    setTimeout(
                        function () {
                            self.progressToComplete(jQuerySelf);
                        }, botTimerMessage
                    );
                    messagesLength = messagesLength - 1;
                    /**
                     * public block answers
                     **/
                    if (messagesLength === 0) {
                        setTimeout(
                            function () {
                                self.getAnswers();
                            }, botTimerMessage + 500
                        );
                    }
                });
            }
            
        });
    }
};

(function() {
    if (typeof(dataBot) !== 'undefined' && dataBot !== '') {
        pupugaBot.getMessagesBot();
    }
}());

