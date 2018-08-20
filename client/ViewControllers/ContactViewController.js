$(function(){
	$(".primary-button").click(VerifyForm);
});

function VerifyForm()
{
	RemoveError();
	var all = true;
	$("input").each(function(idx, item)
	{
		if(!$(item).val())
		{
			ShowError("Please fill out all fields before sending.", $(item).parent().parent().prev());
			all = false;
			return false;
		}
	});
	if(!all) return false;
	
	if(!$("textarea").val())
	{
		ShowError("Please fill out all fields before sending.", $("tr.message"));
		return false;
	}
		
	if($("input.verification").val().toLowerCase() != "cccc")
	{
		ShowError("Please verify you are not a robot before sending.", $("tr.robot"));
		return false;
	}
	
	//TODO: actually send the email
	
	$("input").val("").prop("disabled", true);
	$("textarea").val("").prop("disabled", true);
	
	$(".thanks").slideDown("slow");
}