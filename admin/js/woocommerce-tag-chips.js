var $$= jQuery;

jQuery(document).ready(function(){
	if ( $$('#chiptext').val() ) {
		$$('.white-display-box').show();
		$$('#chip-demo').show();
	} else {
		$$('.white-display-box').hide();
		$$('#chip-demo').hide();
	}
	$$('#chiptext').on('change keydown paste input', updateChip); // Update chip preview
	$$('#chipbgcolor').on('change keydown paste input', updateChipColor); // Update chip preview
	$$('#chiptxtcolor').on('change keydown paste input', updateChipTxtColor); // Update chip preview
	$$('#submit').on('click', emptyFields); // Clear chip preview on submit
});

const updateChip= function(){
	if($$('#chiptext').val()){
		$$('.white-display-box').show();
		$$('#chip-demo').show();
		$$('.tag-chip').text($$('#chiptext').val());
	} else {
		$$('.white-display-box').hide();
		$$('#chip-demo').hide();
	}
}
const updateChipColor= function(){
	if($$('#chipbgcolor').val()){
		let colorVal= $$('#chipbgcolor').val();
		if(/^[0-9a-f]+$/.test(colorVal)){
			colorVal= '#'+colorVal;
		} else if(/^#[0-9a-f]+$/.test(colorVal)){
			colorVal= colorVal;
		} else {
			colorVal= colorVal.replace(/[#]/g, '');
		}
		$$('#chipbgcolor').attr( "value", colorVal );
		$$('.tag-chip').css({ "background-color" : colorVal });
	} else {
		$$('.tag-chip').css({ "background-color" : '' });
	}
}
const updateChipTxtColor= function(){
	if($$('#chiptxtcolor').val()){
		let txtColorVal= $$('#chiptxtcolor').val();
		if(/^[0-9a-f]+$/.test(txtColorVal)) {
			txtColorVal= '#'+txtColorVal;
		} else if(/^#[0-9a-f]+$/.test(txtColorVal)){
			txtColorVal= txtColorVal;
		} else {
			txtColorVal= txtColorVal.replace(/[#]/g, '');
		}
		$$('#chiptxtcolor').attr( "value", txtColorVal );
		$$('.tag-chip').css({ "color" : txtColorVal });
	} else {
		$$('.tag-chip').css({ "color" : '' });
	}
}
const emptyFields= function(){
	$$('#chip-demo').hide();
	$$('.tag-chip').css({ "background-color" : '' });
	$$('.tag-chip').css({ "color" : '' });
}
