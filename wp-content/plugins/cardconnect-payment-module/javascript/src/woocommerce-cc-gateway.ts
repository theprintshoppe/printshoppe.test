/// <reference path="./typings/tsd.d.ts"/>
declare let jQuery : any;
declare let wooCardConnect : any;
declare let window : any;
import CardConnectTokenizer from "./card-connect-tokenizer";
import SavedCards from './saved-cards';

const SAVED_CARDS_SELECT = '#card_connect-cards';

jQuery($ => {

  let isLive : boolean = Boolean(wooCardConnect.isLive);
  let cc = new CardConnectTokenizer($, wooCardConnect.apiEndpoint);
  let $form = $('form.checkout, form#order_review');
  let $errors;


  // !! 'updated_checkout' is not fired for the 'payment method change' form aka 'form#order_review'
  $('body').on('updated_checkout', function () {
    //console.log("!!! caught updated_checkout");
    if (wooCardConnect.profilesEnabled)
      SavedCards.init();
  });


  //'updated_checkout' (above) was not fired for the 'payment method change' form aka 'form#order_review'
  // so this was added.
  $('form#order_review').ready( function() {
    //console.log('ready');
    if (wooCardConnect.profilesEnabled) {
      SavedCards.init();
    }
  });


  // Simulate some text entry to get jQuery Payment to reformat numbers
  if(!isLive){
    $('body').on('updated_checkout', ()=>{
      getToken();
    });
  }

  function getToken() : boolean {

    // why is/was this here?
    //if (checkAllowSubmit()) {
    //    return false;
    //}


    let $ccInput = $form.find('#card_connect-card-number');
    let creditCard = $ccInput.val();

    if(creditCard.indexOf('\u2022') > -1) return;

    $form.block({
      message: null,
      overlayCSS: {
        background: '#fff',
        opacity: 0.6
      }
    });
    if(!creditCard){
      printWooError('Please enter a credit card number');
      return false;
    }else if(!checkCardType(creditCard)){
      printWooError('Credit card type not accepted');
      return false;
    }
    cc.getToken(creditCard, function(token, error){
      if(error){
        printWooError(error);
        return false;
      }
      // Append token as hidden input
      $('<input />')
        .attr('name', 'card_connect_token')
        .attr('type', 'hidden')
        .addClass('card-connect-token')
        .val(token)
        .appendTo($form);

      // Mask user entered CC number
      $ccInput.val($.map(creditCard.split(''), (char, index) => {
        if(creditCard.length - (index + 1) > 4 ){
          return char !== ' ' ? '\u2022' : ' ';
        }else{
          return char;
        }
      }).join(''));

    });
    $form.unblock();
    return true;
  }

  function checkAllowSubmit() : boolean {
    // if we have a token OR a 'saved card' is selected, return FALSE
    return 0 !== $('input.card-connect-token', $form).size() || $(SAVED_CARDS_SELECT).val();
  }

  function checkCardType(cardNumber : string) : boolean {
    let cardType = $.payment.cardType(cardNumber);
    for(let i = 0; i < wooCardConnect.allowedCards.length; i++) {
      if(wooCardConnect.allowedCards[i] === cardType) return true;
    }
    return false;
  }

  function printWooError(error : string | string[]) : void {

    if(!$errors) $errors = $('.js-card-connect-errors', $form);

    let errorText : string | string[]; // This should only be a string, TS doesn't like the reduce output though
    if(error.constructor === Array){
      errorText = Array(error).reduce((prev, curr) => prev += `<li>${curr}</li>`);
    }else{
      errorText = `<li>${error}</li>`;
    }

    $errors.html(`<ul class="woocommerce-error">${errorText}</ul>`);
    $form.unblock();
  }

  // Get token when focus of CC field is lost
  $form.on('blur', '#card_connect-card-number', () => {
    if($errors) $errors.html('');
    return getToken();
  });

  // Bind Submit Listeners
  $form.on('checkout_place_order_card_connect', () => checkAllowSubmit());
  $('form#order_review').on('submit', () => checkAllowSubmit());

  // Remove token on checkout err
  $('document.body').on('checkout_error', () => {
    if($errors) $errors.html('');
    $('.card-connect-token').remove();
  });

  // Clear token if form is changed
  $form.on('keyup change', `#card_connect-card-number, ${SAVED_CARDS_SELECT}`, () => {
    $('.card-connect-token').remove();
  });

});
