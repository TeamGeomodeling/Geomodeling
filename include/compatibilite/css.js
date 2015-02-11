/**
 * Script activé si le navigateur est IE8 ou pire
 */
$(function(){
	// Ajoute une balise <meta> qui va forcer le navigateur à se comporter comme IE8 (si c'est bien IE8)
	$('head').first().append('<meta http-equiv="X-UA-Compatible" content="IE=8" />');
	
	// Si ça ne marche toujours pas (IE 6 ou 7), on s'amuse en js
	if(!$.support.boxModel){
		var window_width = $('body').first().width();	// largeur de la fenêtre
		
		var body = $('body').first(), 	// <body>
			body_width = 1100;			// largeur de <body>
		
		// Centrage de body
		var body_margin = ((window_width - body_width) / 2) + $(window).scrollLeft();
		body.css('margin-left', body_margin/2);
		body.css('margin-right', body_margin/2);
	}
	
	// Polyfill de l'attribut placeholder
	var input = $('input');
	input.each(function(index, inp){
		if(inp.placeholder && inp.value == ''){
			$inp = $(inp);
			$inp.css('color', 'grey');
			inp.value = inp.placeholder;
			$inp.focus(function(){ if(inp.value == inp.placeholder){inp.value = ''; $inp.css('color', 'black');} });
			$inp.blur(function(){ if(inp.value == ''){inp.value = inp.placeholder; $inp.css('color', 'grey');} });
		}
	});
	// Problème : les placeholders seront envoyés en tant que values avec le formulaire si les champs sont vides, on doit donc les retirer
	$('form').each(function(index, form){
		$form = $(form);
		$form.submit(function(){
			$($form.children()[0]).children().each(function(index, inp){
				if(inp.placeholder && inp.value && inp.value == inp.placeholder)
					inp.value = '';
			});
		});
	});
	
	// Polyfills des pages spéciales - il suffira dans la page de créer une variable page avec pour valeur le nom de la page
	if(page){
		switch(page){
			case 'recherche' :
				$('#contenu table tr').mouseover(function(){ $(this).children('td').css('background-color', '#77FFED'); });
				$('#contenu table tr').mouseleave(function(){ $(this).children('td').css('background-color', 'white'); });
				break;
		}
	}
});