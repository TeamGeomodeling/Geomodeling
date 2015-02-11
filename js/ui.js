/**
 * Partie js du design de l'interface
 */
$(function(){
	// Infobulle de #to_top
	$('#to_top div').mouseover(function(){ $('#to_top_menu .tooltip').show(400); });
	$('#to_top div').mouseleave(function(){ $('#to_top_menu .tooltip').hide(400); });
	$('#to_top div').click(function(){ $('#to_top_menu .tooltip').hide(400); });
	
	// Ajoute un - entre chaque <li> du pied de page
	var footer_li = $('footer li');
	footer_li.each(function(index, li){
		if(index < footer_li.length-1){
			li = $(li);
			li.html(li.html() + ' - ');
		}
	});
	
	// Donne une largeur uniforme aux <td> de la barre de navigation
	var nav_td = $('#top_menu table td');
	nav_td_length = nav_td.length;
	nav_td.each(function(index, td){ $(td).css('width', (100/nav_td_length)+'%'); });
	
	// Bouton de déconnexion - apparition / disparition - code exécuté seulement s'il y a un utilisateur connecté
	if(document.getElementById('utilisateur').innerText != "Pas d'utilisateur connecté"){
		var on_deco_button = false, // a true si la souris est positionnée sur le bouton de déconnexion
			on_name_button = false;
		$('#utilisateur').mouseover(function(){ on_name_button = true; $('#deconnexion').show(400); });
		$('#utilisateur').mouseleave(function(){ on_name_button = false; setTimeout(function(){ if(!(on_deco_button || on_name_button)) $('#deconnexion').hide(400); }, 2000); });
		$('#deconnexion').mouseover(function(){ on_deco_button = true; });
		$('#deconnexion').mouseleave(function(){ on_deco_button = false; setTimeout(function(){ if(!on_deco_button) $('#deconnexion').hide(400); }, 2000); });
	}
});