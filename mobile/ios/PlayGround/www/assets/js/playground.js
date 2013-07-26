var clubList = [];
var restaurantList = [];
var shoppingList = [];
var eventList = [];
var currentUser = {phone_number:'',id:-1};
var db;
var user_phone_number = '';
var msgList = [];
var contactList = [];
var is_device_ready = false;
var photoGallery = [];
var videoGallery = [];

$(document).bind('mobileinit', function(){
	eventList = [];
	clubList = [];
	restaurantList = [];
	shoppingList = [];
	currentUser = {phone_number:'',id:-1};
	contactList = [];
	msgList = [];
	is_device_ready = false;
	photoGallery = [];
	videoGallery = [];
	
	$.extend($.mobile,{
		defaultPageTransition: 'slide'
	});
});

function getPostCategory(post){
	var cat = {id:-1,name:'',parent:-1};
	post.terms.forEach(function(term){
		if (term.taxonomy == 'category'){
			cat.id = term.term_id;
			cat.name = term.name;
			cat.parent = term.parent;
		}
	});
	return cat;
}

function getSquareThumbnail(original_link){
	var strLink = original_link.substring(0,original_link.lastIndexOf('250x'));
	var strSize = original_link.substring(original_link.lastIndexOf('250x'));
	var arrSize = strSize.split('.');
	arrSize[0] = '150x150';
	strSize = '';
	arrSize.forEach(function(part){
		strSize += (part + '.');
	});
	strSize = strSize.substring(0,strSize.length-1);
	strLink = strLink + strSize;
	return strLink;
}

function getFullSizeImg(original_link){
	var strLink = original_link.substring(0,original_link.lastIndexOf('-250x'));
	var strSize = original_link.substring(original_link.lastIndexOf('-250x'));
	var arrSize = strSize.split('.');
	arrSize[0] = '';
	strSize = '';
	arrSize.forEach(function(part){
		strSize += (part + '.');
	});
	strSize = strSize.substring(0,strSize.length-1);
	strLink = strLink + strSize;
	return strLink;
}

function parseEvent(post){
	var meta = {
		id: post.post_id,
		name: post.post_title,
		post_content:post.post_content,
		address: '',
		direction: '',
		date: '',
		time: '',
		poster_link: '',
		poster_thumbnail_link: '',
		map: ''
	};
	
	post.custom_fields.forEach(function(field){
		if (field.key == 'snbp_event_venue'){
			meta.address = field.value;
		} else if (field.key == 'snbp_event_location'){
			meta.direction = field.value;
		} else if (field.key == 'snbp_event_date'){
			meta.date = field.value;
		} else if (field.key == 'snbp_event_time'){
			meta.time = field.value;
		} else if (field.key == 'snbp_pitemlink'){
			meta.poster_thumbnail_link = getSquareThumbnail(field.value);
			meta.poster_link = getFullSizeImg(field.value);
		} else if (field.key == 'snbp_event_map'){
			meta.map = field.value;
		}
	});
	
	return meta;
}

function external_link(link){
	if (link.indexOf('http://') != 0){
		return 'http://' + link;
	} else {
		return link;
	}
}

function getEventInfo(post_id){
	var event_info = false;
	eventList.forEach(function(post){
		if (post.post_id == post_id){
			event_info = post;
			return;
		}
	});
	return event_info;
}

function showLoading(msg){
	if (msg == null){
		msg = "Đang tải dữ liệu... Vui lòng chờ.";
	}
	$.mobile.showPageLoadingMsg("a", msg);
}

function hideLoading(){
	$.mobile.hidePageLoadingMsg();
}

function getUrlVars() {
	var vars = [], hash;
	var hashes;

	if ($.mobile.activePage.data('url').indexOf("?") != -1){ // first check the active page url for parameters
		hashes = $.mobile.activePage.data('url').slice($.mobile.activePage.data('url').indexOf('?') + 1).split('&');
	} else { // otherwise just get the current url
		hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	}

	for(var i = 0; i < hashes.length; i++) {
		hash = hashes[i].split('=');

		if(hash.length > 1 && hash[1].indexOf("#") == -1){ // filter out misinterpreted paramters caused by JQM url naming scheme
			vars[hash[0]] = hash[1];
		}
	}

	return vars;
}

function normalize(str) {
  str= str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");  
  str= str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");  
  str= str.replace(/ì|í|ị|ỉ|ĩ/g,"i");  
  str= str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");  
  str= str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");  
  str= str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");  
  str= str.replace(/đ/g,"d");
  
//  str= str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/G,"A");  
//  str= str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/G,"E");  
//  str= str.replace(/Ì|Í|Ị|Ỉ|Ĩ/G,"I");  
//  str= str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/G,"O");  
//  str= str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/G,"U");  
//  str= str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/G,"Y");  
//  str= str.replace(/Đ/G,"D");
  
  str= str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'|\"|\&|\#|\[|\]|~|$|_/g,"-");
  str= str.replace(/-+-/g,"-");
  str= str.replace(/^\-+|\-+$/g,"");  
  return str;  
  }
  


//get new messge
function getNewMessages(){
	wpJSON.request(
		'pg.getNewMessages',
		{
			'phone_number':user_phone_number
		},
		function (result) {
			if (result.length > 0){
				var has_new_message = false;
				var newMsgCount = 0;
				if (msgList.length == 0){
					msgList = result;
					msgList.forEach(function(msg){
						if (msg.read == 0){
							has_new_message = true;
							newMsgCount++;
						}
					});
					updateMessageSenderName();
				} else {
					var cached_msg_ids = [];
					msgList.forEach(function(m){
						cached_msg_ids.push(m.id);
					});
					result.forEach(function(msg){
						if (cached_msg_ids.indexOf(msg.id) == -1){
							var contact_name = msg.sender;
							contactList.forEach(function(contact){
								contact.phoneNumbers.forEach(function(number){
									if (number.value.replace(' ', '') == msg.sender.replace(' ','')){
										contact_name = contact.name.formatted;
									}
								});
							});
							msg.contact_name = contact_name;
							msgList.unshift(msg);
							if (msg.read == 0){
								has_new_message = true;
								newMsgCount++;
							}
						}
					});
				}
				
				if (has_new_message){
					navigator.notification.confirm(
						'Bạn có ' + newMsgCount + ' tin nhắn mới.',
						function(buttonIndex){
							if (buttonIndex == 1){
								$.mobile.changePage('inbox.html');
							}
						},
						'PlayGround',
						'Xem,Bỏ qua'
					);
				}
			}
		},
		function( code, error, data ) {
			console.log('error: ' + error);
		}
	);
}

$(document).on('pageshow',function(){
	if (user_phone_number != ''){
		getNewMessages();
	}
	if (contactList.length <= 0){
		getContacts();
	}
});

function getContacts(){
	if (!is_device_ready){
		return;
	}
	var options = new ContactFindOptions();
	options.filter = "";
	options.multiple = true;
	var fields = ["*"];
	
	navigator.contacts.find(fields, onContactSuccess, onContactError, options);
}

function onContactSuccess(contacts) {
	contactList = contacts;
	contactList.sort(function(a,b){
		var nameA = a.name.formatted;
		var nameB = b.name.formatted;
		if (nameA < nameB)
			return -1
		if (nameA > nameB)
			return 1
		return 0
	});
	updateMessageSenderName();
}

// onError: Failed to get the contacts

function onContactError(contactError) {
	console.log('onError!');
}

function updateMessageSenderName(){
	msgList.forEach(function(msg){
		var contact_name = msg.sender;
		contactList.forEach(function(contact){
			if (contact.phoneNumbers != null){
				contact.phoneNumbers.forEach(function(number){
					if (number.value.replace(/\s/g,'').replace(/-/g,'').replace(/\(|\)/g, '') ==
						contact_name.replace(/\s/g,'').replace(/-/g,'').replace(/\(|\)/g, '')){
						contact_name = contact.name.formatted;
					}
				});
			}
		});
		msg.contact_name = contact_name;
	});
}

//hungdd
function isNullOrEmpty(obj, strReplaceTo) {
    if (strReplaceTo == null || strReplaceTo == undefined) strReplaceTo = "";
    if (obj == null || obj == undefined || obj == "")
        return strReplaceTo;
    else
        return obj.toString();
}