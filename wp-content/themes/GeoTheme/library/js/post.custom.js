/**
 * @preserve Galleria v 1.2.7a2 2012-01-12
 * http://galleria.aino.se
 *
 * Copyright (c) 2012, Aino
 * Licensed under the MIT license.
 */

/*global jQuery, navigator, Galleria:true, Image */

(function($){var undef,window=this,doc=window.document,$doc=$(doc),$win=$(window),VERSION=1.26,DEBUG=true,TIMEOUT=30000,DUMMY=false,NAV=navigator.userAgent.toLowerCase(),HASH=window.location.hash.replace(/#\//,''),IE=(function(){var v=3,div=doc.createElement('div'),all=div.getElementsByTagName('i');do{div.innerHTML='<!--[if gt IE '+(++v)+']><i></i><![endif]-->'}while(all[0]);return v>4?v:undef}()),DOM=function(){return{html:doc.documentElement,body:doc.body,head:doc.getElementsByTagName('head')[0],title:doc.title}},_eventlist='data ready thumbnail loadstart loadfinish image play pause progress '+'fullscreen_enter fullscreen_exit idle_enter idle_exit rescale '+'lightbox_open lightbox_close lightbox_image',_events=(function(){var evs=[];$.each(_eventlist.split(' '),function(i,ev){evs.push(ev);if(/_/.test(ev)){evs.push(ev.replace(/_/g,''))}});return evs}()),_legacyOptions=function(options){var n;if(typeof options!=='object'){return options}$.each(options,function(key,value){if(/^[a-z]+_/.test(key)){n='';$.each(key.split('_'),function(i,k){n+=i>0?k.substr(0,1).toUpperCase()+k.substr(1):k});options[n]=value;delete options[key]}});return options},_patchEvent=function(type){if($.inArray(type,_events)>-1){return Galleria[type.toUpperCase()]}return type},_timeouts={trunk:{},add:function(id,fn,delay,loop){id=id||new Date().getTime();loop=loop||false;this.clear(id);if(loop){var old=fn;fn=function(){old();_timeouts.add(id,fn,delay)}}this.trunk[id]=window.setTimeout(fn,delay)},clear:function(id){var del=function(i){window.clearTimeout(this.trunk[i]);delete this.trunk[i]},i;if(!!id&&id in this.trunk){del.call(_timeouts,id)}else if(typeof id==='undefined'){for(i in this.trunk){if(this.trunk.hasOwnProperty(i)){del.call(_timeouts,i)}}}}},_galleries=[],_instances=[],_hasError=false,_canvas=false,_pool=[],_themeLoad=function(theme){Galleria.theme=theme;$.each(_pool,function(i,instance){if(!instance._initialized){instance._init.call(instance)}})},Utils=(function(){return{array:function(obj){return Array.prototype.slice.call(obj,0)},create:function(className,nodeName){nodeName=nodeName||'div';var elem=doc.createElement(nodeName);elem.className=className;return elem},getScriptPath:function(src){src=src||$('script:last').attr('src');var slices=src.split('/');if(slices.length==1){return''}slices.pop();return slices.join('/')+'/'},animate:(function(){var transition=(function(style){var props='transition WebkitTransition MozTransition OTransition'.split(' '),i;if(window.opera){return false}for(i=0;props[i];i++){if(typeof style[props[i]]!=='undefined'){return props[i]}}return false}((doc.body||doc.documentElement).style));var endEvent={MozTransition:'transitionend',OTransition:'oTransitionEnd',WebkitTransition:'webkitTransitionEnd',transition:'transitionend'}[transition];var easings={_default:[0.25,0.1,0.25,1],galleria:[0.645,0.045,0.355,1],galleriaIn:[0.55,0.085,0.68,0.53],galleriaOut:[0.25,0.46,0.45,0.94],ease:[0.25,0,0.25,1],linear:[0.25,0.25,0.75,0.75],'ease-in':[0.42,0,1,1],'ease-out':[0,0,0.58,1],'ease-in-out':[0.42,0,0.58,1]};var setStyle=function(elem,value,suffix){var css={};suffix=suffix||'transition';$.each('webkit moz ms o'.split(' '),function(){css['-'+this+'-'+suffix]=value});elem.css(css)};var clearStyle=function(elem){setStyle(elem,'none','transition');if(Galleria.WEBKIT&&Galleria.TOUCH){setStyle(elem,'translate3d(0,0,0)','transform');if(elem.data('revert')){elem.css(elem.data('revert'));elem.data('revert',null)}}};var change,strings,easing,syntax,revert,form,css;return function(elem,to,options){options=$.extend({duration:400,complete:function(){},stop:false},options);elem=$(elem);if(!options.duration){elem.css(to);options.complete.call(elem[0]);return}if(!transition){elem.animate(to,options);return}if(options.stop){elem.unbind(endEvent);clearStyle(elem)}change=false;$.each(to,function(key,val){css=elem.css(key);if(Utils.parseValue(css)!=Utils.parseValue(val)){change=true}elem.css(key,css)});if(!change){window.setTimeout(function(){options.complete.call(elem[0])},options.duration);return}strings=[];easing=options.easing in easings?easings[options.easing]:easings._default;syntax=' '+options.duration+'ms'+' cubic-bezier('+easing.join(',')+')';window.setTimeout(function(){elem.one(endEvent,(function(elem){return function(){clearStyle(elem);options.complete.call(elem[0])}}(elem)));if(Galleria.WEBKIT&&Galleria.TOUCH){revert={};form=[0,0,0];$.each(['left','top'],function(i,m){if(m in to){form[i]=(Utils.parseValue(to[m])-Utils.parseValue(elem.css(m)))+'px';revert[m]=to[m];delete to[m]}});if(form[0]||form[1]){elem.data('revert',revert);strings.push('-webkit-transform'+syntax);setStyle(elem,'translate3d('+form.join(',')+')','transform')}}$.each(to,function(p,val){strings.push(p+syntax)});setStyle(elem,strings.join(','));elem.css(to)},1)}}()),removeAlpha:function(elem){if(IE<9&&elem){var style=elem.style,currentStyle=elem.currentStyle,filter=currentStyle&&currentStyle.filter||style.filter||"";if(/alpha/.test(filter)){style.filter=filter.replace(/alpha\([^)]*\)/i,'')}}},forceStyles:function(elem,styles){elem=$(elem);if(elem.attr('style')){elem.data('styles',elem.attr('style')).removeAttr('style')}elem.css(styles)},revertStyles:function(){$.each(Utils.array(arguments),function(i,elem){elem=$(elem);elem.removeAttr('style');elem.attr('style','');if(elem.data('styles')){elem.attr('style',elem.data('styles')).data('styles',null)}})},moveOut:function(elem){Utils.forceStyles(elem,{position:'absolute',left:-10000})},moveIn:function(){Utils.revertStyles.apply(Utils,Utils.array(arguments))},elem:function(elem){if(elem instanceof $){return{$:elem,dom:elem[0]}}else{return{$:$(elem),dom:elem}}},hide:function(elem,speed,callback){callback=callback||function(){};var el=Utils.elem(elem),$elem=el.$;elem=el.dom;if(!$elem.data('opacity')){$elem.data('opacity',$elem.css('opacity'))}var style={opacity:0};if(speed){var complete=IE<9&&elem?function(){Utils.removeAlpha(elem);elem.style.visibility='hidden';callback.call(elem)}:callback;Utils.animate(elem,style,{duration:speed,complete:complete,stop:true})}else{if(IE<9&&elem){Utils.removeAlpha(elem);elem.style.visibility='hidden'}else{$elem.css(style)}}},show:function(elem,speed,callback){callback=callback||function(){};var el=Utils.elem(elem),$elem=el.$;elem=el.dom;var saved=parseFloat($elem.data('opacity'))||1,style={opacity:saved};if(speed){if(IE<9){$elem.css('opacity',0);elem.style.visibility='visible'}var complete=IE<9&&elem?function(){if(style.opacity==1){Utils.removeAlpha(elem)}callback.call(elem)}:callback;Utils.animate(elem,style,{duration:speed,complete:complete,stop:true})}else{if(IE<9&&style.opacity==1&&elem){Utils.removeAlpha(elem);elem.style.visibility='visible'}else{$elem.css(style)}}},optimizeTouch:(function(){var node,evs,fakes,travel,evt={},handler=function(e){e.preventDefault();evt=$.extend({},e,true)},attach=function(){this.evt=evt},fake=function(){this.handler.call(node,this.evt)};return function(elem){$(elem).bind('touchend',function(e){node=e.target;travel=true;while(node.parentNode&&node!=e.currentTarget&&travel){evs=$(node).data('events');fakes=$(node).data('fakes');if(evs&&'click'in evs){travel=false;e.preventDefault();$(node).click(handler).click();evs.click.pop();$.each(evs.click,attach);$(node).data('fakes',evs.click);delete evs.click}else if(fakes){travel=false;e.preventDefault();$.each(fakes,fake)}node=node.parentNode}})}}()),addTimer:function(){_timeouts.add.apply(_timeouts,Utils.array(arguments));return this},clearTimer:function(){_timeouts.clear.apply(_timeouts,Utils.array(arguments));return this},wait:function(options){options=$.extend({until:function(){return false},success:function(){},error:function(){Galleria.raise('Could not complete wait function.')},timeout:3000},options);var start=Utils.timestamp(),elapsed,now,fn=function(){now=Utils.timestamp();elapsed=now-start;if(options.until(elapsed)){options.success();return false}if(now>=start+options.timeout){options.error();return false}window.setTimeout(fn,10)};window.setTimeout(fn,10)},toggleQuality:function(img,force){if((IE!==7&&IE!==8)||!img){return}if(typeof force==='undefined'){force=img.style.msInterpolationMode==='nearest-neighbor'}img.style.msInterpolationMode=force?'bicubic':'nearest-neighbor'},insertStyleTag:function(styles){var style=doc.createElement('style');DOM().head.appendChild(style);if(style.styleSheet){style.styleSheet.cssText=styles}else{var cssText=doc.createTextNode(styles);style.appendChild(cssText)}},loadScript:function(url,callback){var done=false,script=$('<scr'+'ipt>').attr({src:url,async:true}).get(0);script.onload=script.onreadystatechange=function(){if(!done&&(!this.readyState||this.readyState==='loaded'||this.readyState==='complete')){done=true;script.onload=script.onreadystatechange=null;if(typeof callback==='function'){callback.call(this,this)}}};DOM().head.appendChild(script)},parseValue:function(val){if(typeof val==='number'){return val}else if(typeof val==='string'){var arr=val.match(/\-?\d|\./g);return arr&&arr.constructor===Array?arr.join('')*1:0}else{return 0}},timestamp:function(){return new Date().getTime()},loadCSS:function(href,id,callback){var link,ready=false,length;$('link[rel=stylesheet]').each(function(){if(new RegExp(href).test(this.href)){link=this;return false}});if(typeof id==='function'){callback=id;id=undef}callback=callback||function(){};if(link){callback.call(link,link);return link}length=doc.styleSheets.length;if($('#'+id).length){$('#'+id).attr('href',href);length--;ready=true}else{link=$('<link>').attr({rel:'stylesheet',href:href,id:id}).get(0);window.setTimeout(function(){var styles=$('link[rel="stylesheet"], style');if(styles.length){styles.get(0).parentNode.insertBefore(link,styles[0])}else{DOM().head.appendChild(link)}if(IE){if(length>=31){Galleria.raise('You have reached the browser stylesheet limit (31)',true);return}link.onreadystatechange=function(e){if(!ready&&(!this.readyState||this.readyState==='loaded'||this.readyState==='complete')){ready=true}}}else{if(!(new RegExp('file://','i').test(href))){$.ajax({url:href,success:function(){ready=true},error:function(e){if(e.isRejected()&&Galleria.WEBKIT){ready=true}}})}else{ready=true}}},10)}if(typeof callback==='function'){Utils.wait({until:function(){return ready&&doc.styleSheets.length>length},success:function(){window.setTimeout(function(){callback.call(link,link)},100)},error:function(){Galleria.raise('Theme CSS could not load',true)},timeout:10000})}return link}}}()),_transitions=(function(){var _slide=function(params,complete,fade,door){var easing=this.getOptions('easing'),distance=this.getStageWidth(),from={left:distance*(params.rewind?-1:1)},to={left:0};if(fade){from.opacity=0;to.opacity=1}else{from.opacity=1}$(params.next).css(from);Utils.animate(params.next,to,{duration:params.speed,complete:(function(elems){return function(){complete();elems.css({left:0})}}($(params.next).add(params.prev))),queue:false,easing:easing});if(door){params.rewind=!params.rewind}if(params.prev){from={left:0};to={left:distance*(params.rewind?1:-1)};if(fade){from.opacity=1;to.opacity=0}$(params.prev).css(from);Utils.animate(params.prev,to,{duration:params.speed,queue:false,easing:easing,complete:function(){$(this).css('opacity',0)}})}};return{fade:function(params,complete){$(params.next).css({opacity:0,left:0}).show();Utils.animate(params.next,{opacity:1},{duration:params.speed,complete:complete});if(params.prev){$(params.prev).css('opacity',1).show();Utils.animate(params.prev,{opacity:0},{duration:params.speed})}},flash:function(params,complete){$(params.next).css({opacity:0,left:0});if(params.prev){Utils.animate(params.prev,{opacity:0},{duration:params.speed/2,complete:function(){Utils.animate(params.next,{opacity:1},{duration:params.speed,complete:complete})}})}else{Utils.animate(params.next,{opacity:1},{duration:params.speed,complete:complete})}},pulse:function(params,complete){if(params.prev){$(params.prev).hide()}$(params.next).css({opacity:0,left:0}).show();Utils.animate(params.next,{opacity:1},{duration:params.speed,complete:complete})},slide:function(params,complete){_slide.apply(this,Utils.array(arguments))},fadeslide:function(params,complete){_slide.apply(this,Utils.array(arguments).concat([true]))},doorslide:function(params,complete){_slide.apply(this,Utils.array(arguments).concat([false,true]))}}}());Galleria=function(){var self=this;this._theme=undef;this._options={};this._playing=false;this._playtime=5000;this._active=null;this._queue={length:0};this._data=[];this._dom={};this._thumbnails=[];this._layers=[];this._initialized=false;this._firstrun=false;this._stageWidth=0;this._stageHeight=0;this._target=undef;this._id=Math.random();var divs='container stage images image-nav image-nav-left image-nav-right '+'info info-text info-title info-description '+'thumbnails thumbnails-list thumbnails-container thumb-nav-left thumb-nav-right '+'loader counter tooltip',spans='current total';$.each(divs.split(' '),function(i,elemId){self._dom[elemId]=Utils.create('galleria-'+elemId)});$.each(spans.split(' '),function(i,elemId){self._dom[elemId]=Utils.create('galleria-'+elemId,'span')});var keyboard=this._keyboard={keys:{'UP':38,'DOWN':40,'LEFT':37,'RIGHT':39,'RETURN':13,'ESCAPE':27,'BACKSPACE':8,'SPACE':32},map:{},bound:false,press:function(e){var key=e.keyCode||e.which;if(key in keyboard.map&&typeof keyboard.map[key]==='function'){keyboard.map[key].call(self,e)}},attach:function(map){var key,up;for(key in map){if(map.hasOwnProperty(key)){up=key.toUpperCase();if(up in keyboard.keys){keyboard.map[keyboard.keys[up]]=map[key]}else{keyboard.map[up]=map[key]}}}if(!keyboard.bound){keyboard.bound=true;$doc.bind('keydown',keyboard.press)}},detach:function(){keyboard.bound=false;keyboard.map={};$doc.unbind('keydown',keyboard.press)}};var controls=this._controls={0:undef,1:undef,active:0,swap:function(){controls.active=controls.active?0:1},getActive:function(){return controls[controls.active]},getNext:function(){return controls[1-controls.active]}};var carousel=this._carousel={next:self.$('thumb-nav-right'),prev:self.$('thumb-nav-left'),width:0,current:0,max:0,hooks:[],update:function(){var w=0,h=0,hooks=[0];$.each(self._thumbnails,function(i,thumb){if(thumb.ready){w+=thumb.outerWidth||$(thumb.container).outerWidth(true);hooks[i+1]=w;h=Math.max(h,thumb.outerHeight||$(thumb.container).outerHeight(true))}});self.$('thumbnails').css({width:w,height:h});carousel.max=w;carousel.hooks=hooks;carousel.width=self.$('thumbnails-list').width();carousel.setClasses();self.$('thumbnails-container').toggleClass('galleria-carousel',w>carousel.width);carousel.width=self.$('thumbnails-list').width()},bindControls:function(){var i;carousel.next.bind('click',function(e){e.preventDefault();if(self._options.carouselSteps==='auto'){for(i=carousel.current;i<carousel.hooks.length;i++){if(carousel.hooks[i]-carousel.hooks[carousel.current]>carousel.width){carousel.set(i-2);break}}}else{carousel.set(carousel.current+self._options.carouselSteps)}});carousel.prev.bind('click',function(e){e.preventDefault();if(self._options.carouselSteps==='auto'){for(i=carousel.current;i>=0;i--){if(carousel.hooks[carousel.current]-carousel.hooks[i]>carousel.width){carousel.set(i+2);break}else if(i===0){carousel.set(0);break}}}else{carousel.set(carousel.current-self._options.carouselSteps)}})},set:function(i){i=Math.max(i,0);while(carousel.hooks[i-1]+carousel.width>=carousel.max&&i>=0){i--}carousel.current=i;carousel.animate()},getLast:function(i){return(i||carousel.current)-1},follow:function(i){if(i===0||i===carousel.hooks.length-2){carousel.set(i);return}var last=carousel.current;while(carousel.hooks[last]-carousel.hooks[carousel.current]<carousel.width&&last<=carousel.hooks.length){last++}if(i-1<carousel.current){carousel.set(i-1)}else if(i+2>last){carousel.set(i-last+carousel.current+2)}},setClasses:function(){carousel.prev.toggleClass('disabled',!carousel.current);carousel.next.toggleClass('disabled',carousel.hooks[carousel.current]+carousel.width>=carousel.max)},animate:function(to){carousel.setClasses();var num=carousel.hooks[carousel.current]*-1;if(isNaN(num)){return}Utils.animate(self.get('thumbnails'),{left:num},{duration:self._options.carouselSpeed,easing:self._options.easing,queue:false})}};var tooltip=this._tooltip={initialized:false,open:false,init:function(){tooltip.initialized=true;var css='.galleria-tooltip{padding:3px 8px;max-width:50%;background:#ffe;color:#000;z-index:3;position:absolute;font-size:11px;line-height:1.3'+'opacity:0;box-shadow:0 0 2px rgba(0,0,0,.4);-moz-box-shadow:0 0 2px rgba(0,0,0,.4);-webkit-box-shadow:0 0 2px rgba(0,0,0,.4);}';Utils.insertStyleTag(css);self.$('tooltip').css('opacity',0.8);Utils.hide(self.get('tooltip'))},move:function(e){var mouseX=self.getMousePosition(e).x,mouseY=self.getMousePosition(e).y,$elem=self.$('tooltip'),x=mouseX,y=mouseY,height=$elem.outerHeight(true)+1,width=$elem.outerWidth(true),limitY=height+15;var maxX=self.$('container').width()-width-2,maxY=self.$('container').height()-height-2;if(!isNaN(x)&&!isNaN(y)){x+=10;y-=30;x=Math.max(0,Math.min(maxX,x));y=Math.max(0,Math.min(maxY,y));if(mouseY<limitY){y=limitY}$elem.css({left:x,top:y})}},bind:function(elem,value){if(Galleria.TOUCH){return}if(!tooltip.initialized){tooltip.init()}var hover=function(elem,value){tooltip.define(elem,value);$(elem).hover(function(){Utils.clearTimer('switch_tooltip');self.$('container').unbind('mousemove',tooltip.move).bind('mousemove',tooltip.move).trigger('mousemove');tooltip.show(elem);Galleria.utils.addTimer('tooltip',function(){self.$('tooltip').stop().show().animate({opacity:1});tooltip.open=true},tooltip.open?0:500)},function(){self.$('container').unbind('mousemove',tooltip.move);Utils.clearTimer('tooltip');self.$('tooltip').stop().animate({opacity:0},200,function(){self.$('tooltip').hide();Utils.addTimer('switch_tooltip',function(){tooltip.open=false},1000)})})};if(typeof value==='string'){hover((elem in self._dom?self.get(elem):elem),value)}else{$.each(elem,function(elemID,val){hover(self.get(elemID),val)})}},show:function(elem){elem=$(elem in self._dom?self.get(elem):elem);var text=elem.data('tt'),mouseup=function(e){window.setTimeout((function(ev){return function(){tooltip.move(ev)}}(e)),10);elem.unbind('mouseup',mouseup)};text=typeof text==='function'?text():text;if(!text){return}self.$('tooltip').html(text.replace(/\s/,'&nbsp;'));elem.bind('mouseup',mouseup)},define:function(elem,value){if(typeof value!=='function'){var s=value;value=function(){return s}}elem=$(elem in self._dom?self.get(elem):elem).data('tt',value);tooltip.show(elem)}};var fullscreen=this._fullscreen={scrolled:0,crop:undef,transition:undef,active:false,keymap:self._keyboard.map,enter:function(callback){fullscreen.active=true;Utils.hide(self.getActiveImage());self.$('container').addClass('fullscreen');fullscreen.scrolled=$win.scrollTop();Utils.forceStyles(self.get('container'),{position:'fixed',top:0,left:0,width:'100%',height:'100%',zIndex:10000});var htmlbody={height:'100%',overflow:'hidden',margin:0,padding:0},data=self.getData(),options=self._options;Utils.forceStyles(DOM().html,htmlbody);Utils.forceStyles(DOM().body,htmlbody);fullscreen.keymap=$.extend({},self._keyboard.map);self.attachKeyboard({escape:self.exitFullscreen,right:self.next,left:self.prev});fullscreen.crop=options.imageCrop;if(options.fullscreenCrop!=undef){options.imageCrop=options.fullscreenCrop}if(data&&data.big&&data.image!==data.big){var big=new Galleria.Picture(),cached=big.isCached(data.big),index=self.getIndex(),thumb=self._thumbnails[index];self.trigger({type:Galleria.LOADSTART,cached:cached,rewind:false,index:index,imageTarget:self.getActiveImage(),thumbTarget:thumb});big.load(data.big,function(big){self._scaleImage(big,{complete:function(big){self.trigger({type:Galleria.LOADFINISH,cached:cached,index:index,rewind:false,imageTarget:big.image,thumbTarget:thumb});var image=self._controls.getActive().image;if(image){$(image).width(big.image.width).height(big.image.height).attr('style',$(big.image).attr('style')).attr('src',big.image.src)}}})})}self.rescale(function(){Utils.addTimer('fullscreen_enter',function(){Utils.show(self.getActiveImage());if(typeof callback==='function'){callback.call(self)}},100);self.trigger(Galleria.FULLSCREEN_ENTER)});$win.resize(function(){fullscreen.scale()})},scale:function(){self.rescale()},exit:function(callback){fullscreen.active=false;Utils.hide(self.getActiveImage());self.$('container').removeClass('fullscreen');Utils.revertStyles(self.get('container'),DOM().html,DOM().body);window.scrollTo(0,fullscreen.scrolled);self.detachKeyboard();self.attachKeyboard(fullscreen.keymap);self._options.imageCrop=fullscreen.crop;var big=self.getData().big,image=self._controls.getActive().image;if(big&&big==image.src){window.setTimeout(function(src){return function(){image.src=src}}(self.getData().image),1)}self.rescale(function(){Utils.addTimer('fullscreen_exit',function(){Utils.show(self.getActiveImage());if(typeof callback==='function'){callback.call(self)}},50);self.trigger(Galleria.FULLSCREEN_EXIT)});$win.unbind('resize',fullscreen.scale)}};var idle=this._idle={trunk:[],bound:false,add:function(elem,to){if(!elem){return}if(!idle.bound){idle.addEvent()}elem=$(elem);var from={},style;for(style in to){if(to.hasOwnProperty(style)){from[style]=elem.css(style)}}elem.data('idle',{from:from,to:to,complete:true,busy:false});idle.addTimer();idle.trunk.push(elem)},remove:function(elem){elem=jQuery(elem);$.each(idle.trunk,function(i,el){if(el.length&&!el.not(elem).length){self._idle.show(elem);self._idle.trunk.splice(i,1)}});if(!idle.trunk.length){idle.removeEvent();Utils.clearTimer('idle')}},addEvent:function(){idle.bound=true;self.$('container').bind('mousemove click',idle.showAll)},removeEvent:function(){idle.bound=false;self.$('container').unbind('mousemove click',idle.showAll)},addTimer:function(){Utils.addTimer('idle',function(){self._idle.hide()},self._options.idleTime)},hide:function(){if(!self._options.idleMode){return}self.trigger(Galleria.IDLE_ENTER);$.each(idle.trunk,function(i,elem){var data=elem.data('idle');if(!data){return}elem.data('idle').complete=false;Utils.animate(elem,data.to,{duration:self._options.idleSpeed})})},showAll:function(){Utils.clearTimer('idle');$.each(self._idle.trunk,function(i,elem){self._idle.show(elem)})},show:function(elem){var data=elem.data('idle');if(!data.busy&&!data.complete){data.busy=true;self.trigger(Galleria.IDLE_EXIT);Utils.clearTimer('idle');Utils.animate(elem,data.from,{duration:self._options.idleSpeed/2,complete:function(){$(this).data('idle').busy=false;$(this).data('idle').complete=true}})}idle.addTimer()}};var lightbox=this._lightbox={width:0,height:0,initialized:false,active:null,image:null,elems:{},keymap:false,init:function(){self.trigger(Galleria.LIGHTBOX_OPEN);if(lightbox.initialized){return}lightbox.initialized=true;var elems='overlay box content shadow title info close prevholder prev nextholder next counter image',el={},op=self._options,css='',abs='position:absolute;',prefix='lightbox-',cssMap={overlay:'position:fixed;display:none;opacity:'+op.overlayOpacity+';filter:alpha(opacity='+(op.overlayOpacity*100)+');top:0;left:0;width:100%;height:100%;background:'+op.overlayBackground+';z-index:99990',box:'position:fixed;display:none;width:400px;height:400px;top:50%;left:50%;margin-top:-200px;margin-left:-200px;z-index:99991',shadow:abs+'background:#000;width:100%;height:100%;',content:abs+'background-color:#fff;top:10px;left:10px;right:10px;bottom:10px;overflow:hidden',info:abs+'bottom:10px;left:10px;right:10px;color:#444;font:11px/13px arial,sans-serif;height:13px',close:abs+'top:10px;right:10px;height:20px;width:20px;background:#fff;text-align:center;cursor:pointer;color:#444;font:16px/22px arial,sans-serif;z-index:99999',image:abs+'top:10px;left:10px;right:10px;bottom:30px;overflow:hidden;display:block;',prevholder:abs+'width:50%;top:0;bottom:40px;cursor:pointer;',nextholder:abs+'width:50%;top:0;bottom:40px;right:-1px;cursor:pointer;',prev:abs+'top:50%;margin-top:-20px;height:40px;width:30px;background:#fff;left:20px;display:none;text-align:center;color:#000;font:bold 16px/36px arial,sans-serif',next:abs+'top:50%;margin-top:-20px;height:40px;width:30px;background:#fff;right:20px;left:auto;display:none;font:bold 16px/36px arial,sans-serif;text-align:center;color:#000',title:'float:left',counter:'float:right;margin-left:8px;'},hover=function(elem){return elem.hover(function(){$(this).css('color','#bbb')},function(){$(this).css('color','#444')})},appends={};if(IE&&IE>7){cssMap.nextholder+='background:#000;filter:alpha(opacity=0);';cssMap.prevholder+='background:#000;filter:alpha(opacity=0);'}$.each(cssMap,function(key,value){css+='.galleria-'+prefix+key+'{'+value+'}'});Utils.insertStyleTag(css);$.each(elems.split(' '),function(i,elemId){self.addElement('lightbox-'+elemId);el[elemId]=lightbox.elems[elemId]=self.get('lightbox-'+elemId)});lightbox.image=new Galleria.Picture();$.each({box:'shadow content close prevholder nextholder',info:'title counter',content:'info image',prevholder:'prev',nextholder:'next'},function(key,val){var arr=[];$.each(val.split(' '),function(i,prop){arr.push(prefix+prop)});appends[prefix+key]=arr});self.append(appends);$(el.image).append(lightbox.image.container);$(DOM().body).append(el.overlay,el.box);Utils.optimizeTouch(el.box);hover($(el.close).bind('click',lightbox.hide).html('&#215;'));$.each(['Prev','Next'],function(i,dir){var $d=$(el[dir.toLowerCase()]).html(/v/.test(dir)?'&#8249;&nbsp;':'&nbsp;&#8250;'),$e=$(el[dir.toLowerCase()+'holder']);$e.bind('click',function(){lightbox['show'+dir]()});if(IE<8||Galleria.TOUCH){$d.show();return}$e.hover(function(){$d.show()},function(e){$d.stop().fadeOut(200)})});$(el.overlay).bind('click',lightbox.hide);if(Galleria.IPAD){self._options.lightboxTransitionSpeed=0}},rescale:function(event){var width=Math.min($win.width()-40,lightbox.width),height=Math.min($win.height()-60,lightbox.height),ratio=Math.min(width/lightbox.width,height/lightbox.height),destWidth=Math.round(lightbox.width*ratio)+40,destHeight=Math.round(lightbox.height*ratio)+60,to={width:destWidth,height:destHeight,'margin-top':Math.ceil(destHeight/2)*-1,'margin-left':Math.ceil(destWidth/2)*-1};if(event){$(lightbox.elems.box).css(to)}else{$(lightbox.elems.box).animate(to,{duration:self._options.lightboxTransitionSpeed,easing:self._options.easing,complete:function(){var image=lightbox.image,speed=self._options.lightboxFadeSpeed;self.trigger({type:Galleria.LIGHTBOX_IMAGE,imageTarget:image.image});$(image.container).show();Utils.show(image.image,speed);Utils.show(lightbox.elems.info,speed)}})}},hide:function(){lightbox.image.image=null;$win.unbind('resize',lightbox.rescale);$(lightbox.elems.box).hide();Utils.hide(lightbox.elems.info);self.detachKeyboard();self.attachKeyboard(lightbox.keymap);lightbox.keymap=false;Utils.hide(lightbox.elems.overlay,200,function(){$(this).hide().css('opacity',self._options.overlayOpacity);self.trigger(Galleria.LIGHTBOX_CLOSE)})},showNext:function(){lightbox.show(self.getNext(lightbox.active))},showPrev:function(){lightbox.show(self.getPrev(lightbox.active))},show:function(index){lightbox.active=index=typeof index==='number'?index:self.getIndex();if(!lightbox.initialized){lightbox.init()}if(!lightbox.keymap){lightbox.keymap=$.extend({},self._keyboard.map);self.attachKeyboard({escape:lightbox.hide,right:lightbox.showNext,left:lightbox.showPrev})}$win.unbind('resize',lightbox.rescale);var data=self.getData(index),total=self.getDataLength(),n=self.getNext(index),ndata,p,i;Utils.hide(lightbox.elems.info);try{for(i=self._options.preload;i>0;i--){p=new Galleria.Picture();ndata=self.getData(n);p.preload('big'in ndata?ndata.big:ndata.image);n=self.getNext(n)}}catch(e){}lightbox.image.load(data.big||data.image,function(image){lightbox.width=image.original.width;lightbox.height=image.original.height;$(image.image).css({width:'100.5%',height:'100.5%',top:0,zIndex:99998});Utils.hide(image.image);lightbox.elems.title.innerHTML=data.title||'';lightbox.elems.counter.innerHTML=(index+1)+' / '+total;$win.resize(lightbox.rescale);lightbox.rescale()});$(lightbox.elems.overlay).show().css('visibility','visible');$(lightbox.elems.box).show()}};return this};Galleria.prototype={constructor:Galleria,init:function(target,options){var self=this;options=_legacyOptions(options);this._original={target:target,options:options,data:null};this._target=this._dom.target=target.nodeName?target:$(target).get(0);_instances.push(this);if(!this._target){Galleria.raise('Target not found.',true);return}this._options={autoplay:false,carousel:true,carouselFollow:true,carouselSpeed:400,carouselSteps:'auto',clicknext:false,dataConfig:function(elem){return{}},dataSelector:'img',dataSource:this._target,debug:undef,dummy:undef,easing:'galleria',extend:function(options){},fullscreenCrop:undef,fullscreenDoubleTap:true,fullscreenTransition:undef,height:'auto',idleMode:true,idleTime:3000,idleSpeed:200,imageCrop:false,imageMargin:0,imagePan:false,imagePanSmoothness:12,imagePosition:'50%',imageTimeout:undef,initialTransition:undef,keepSource:false,layerFollow:true,lightbox:false,lightboxFadeSpeed:200,lightboxTransitionSpeed:200,linkSourceImages:true,maxScaleRatio:undef,minScaleRatio:undef,overlayOpacity:0.85,overlayBackground:'#0b0b0b',pauseOnInteraction:true,popupLinks:false,preload:2,queue:true,show:0,showInfo:true,showCounter:true,showImagenav:true,swipe:true,thumbCrop:true,thumbEventType:'click',thumbFit:true,thumbMargin:0,thumbQuality:'auto',thumbnails:true,touchTransition:undef,transition:'fade',transitionInitial:undef,transitionSpeed:400,useCanvas:false,width:'auto'};this._options.initialTransition=this._options.initialTransition||this._options.transitionInitial;if(options&&options.debug===false){DEBUG=false}if(options&&typeof options.imageTimeout==='number'){TIMEOUT=options.imageTimeout}if(options&&typeof options.dummy==='string'){DUMMY=options.dummy}$(this._target).children().hide();if(typeof Galleria.theme==='object'){this._init()}else{_pool.push(this)}return this},_init:function(){var self=this,options=this._options;if(this._initialized){Galleria.raise('Init failed: Gallery instance already initialized.');return this}this._initialized=true;if(!Galleria.theme){Galleria.raise('Init failed: No theme found.');return this}$.extend(true,options,Galleria.theme.defaults,this._original.options);(function(can){if(!('getContext'in can)){can=null;return}_canvas=_canvas||{elem:can,context:can.getContext('2d'),cache:{},length:0}}(doc.createElement('canvas')));this.bind(Galleria.DATA,function(){if(Galleria.QUIRK){Galleria.raise('Your page is in Quirks mode, Galleria may not render correctly. Please validate your HTML.')}this._original.data=this._data;this.get('total').innerHTML=this.getDataLength();var $container=this.$('container');var num={width:0,height:0};var testHeight=function(){return self.$('stage').height()};Utils.wait({until:function(){$.each(['width','height'],function(i,m){if(options[m]&&typeof options[m]==='number'){num[m]=options[m]}else{num[m]=Math.max(Utils.parseValue($container.css(m)),Utils.parseValue(self.$('target').css(m)),$container[m](),self.$('target')[m]())}$container[m](num[m])});return testHeight()&&num.width&&num.height>10},success:function(){if(Galleria.WEBKIT){window.setTimeout(function(){self._run()},1)}else{self._run()}},error:function(){if(testHeight()){Galleria.raise('Could not extract sufficient width/height of the gallery container. Traced measures: width:'+num.width+'px, height: '+num.height+'px.',true)}else{Galleria.raise('Could not extract a stage height from the CSS. Traced height: '+testHeight()+'px.',true)}},timeout:10000})});this.append({'info-text':['info-title','info-description'],'info':['info-text'],'image-nav':['image-nav-right','image-nav-left'],'stage':['images','loader','counter','image-nav'],'thumbnails-list':['thumbnails'],'thumbnails-container':['thumb-nav-left','thumbnails-list','thumb-nav-right'],'container':['stage','thumbnails-container','info','tooltip']});Utils.hide(this.$('counter').append(this.get('current'),doc.createTextNode(' / '),this.get('total')));this.setCounter('&#8211;');Utils.hide(self.get('tooltip'));this.$('container').addClass(Galleria.TOUCH?'touch':'notouch');$.each(new Array(2),function(i){var image=new Galleria.Picture();$(image.container).css({position:'absolute',top:0,left:0}).prepend(self._layers[i]=$(Utils.create('galleria-layer')).css({position:'absolute',top:0,left:0,right:0,bottom:0,zIndex:2})[0]);self.$('images').append(image.container);self._controls[i]=image});this.$('images').css({position:'relative',top:0,left:0,width:'100%',height:'100%'});this.$('thumbnails, thumbnails-list').css({overflow:'hidden',position:'relative'});this.$('image-nav-right, image-nav-left').bind('click',function(e){if(options.clicknext){e.stopPropagation()}if(options.pauseOnInteraction){self.pause()}var fn=/right/.test(this.className)?'next':'prev';self[fn]()});$.each(['info','counter','image-nav'],function(i,el){if(options['show'+el.substr(0,1).toUpperCase()+el.substr(1).replace(/-/,'')]===false){Utils.moveOut(self.get(el.toLowerCase()))}});this.load();if(!options.keepSource&&!IE){this._target.innerHTML=''}if(this.get('errors')){this.appendChild('target','errors')}this.appendChild('target','container');if(options.carousel){var count=0,show=options.show;this.bind(Galleria.THUMBNAIL,function(){this.updateCarousel();if(++count==this.getDataLength()&&typeof show=='number'&&show>0){this._carousel.follow(show)}})}if(options.swipe){(function(images){var swipeStart=[0,0],swipeStop=[0,0],limitX=30,limitY=100,multi=false,tid=0,data,ev={start:'touchstart',move:'touchmove',stop:'touchend'},getData=function(e){return e.originalEvent.touches?e.originalEvent.touches[0]:e},moveHandler=function(e){if(e.originalEvent.touches&&e.originalEvent.touches.length>1){return}data=getData(e);swipeStop=[data.pageX,data.pageY];if(!swipeStart[0]){swipeStart=swipeStop}if(Math.abs(swipeStart[0]-swipeStop[0])>10){e.preventDefault()}},upHandler=function(e){images.unbind(ev.move,moveHandler);if((e.originalEvent.touches&&e.originalEvent.touches.length)||multi){multi=!multi;return}if(Utils.timestamp()-tid<1000&&Math.abs(swipeStart[0]-swipeStop[0])>limitX&&Math.abs(swipeStart[1]-swipeStop[1])<limitY){e.preventDefault();self[swipeStart[0]>swipeStop[0]?'next':'prev']()}swipeStart=swipeStop=[0,0]};images.bind(ev.start,function(e){if(e.originalEvent.touches&&e.originalEvent.touches.length>1){return}data=getData(e);tid=Utils.timestamp();swipeStart=swipeStop=[data.pageX,data.pageY];images.bind(ev.move,moveHandler).one(ev.stop,upHandler)})}(self.$('images')));if(options.fullscreenDoubleTap){this.$('stage').bind('touchstart',(function(){var last,cx,cy,lx,ly,now,getData=function(e){return e.originalEvent.touches?e.originalEvent.touches[0]:e};return function(e){now=Galleria.utils.timestamp();cx=getData(e).pageX;cy=getData(e).pageY;if((now-last<500)&&(cx-lx<20)&&(cy-ly<20)){self.toggleFullscreen();e.preventDefault();self.$('stage').unbind('touchend',arguments.callee);return}last=now;lx=cx;ly=cy}}()))}}Utils.optimizeTouch(this.get('container'));return this},_createThumbnails:function(){this.get('total').innerHTML=this.getDataLength();var i,src,thumb,data,$container,self=this,o=this._options,active=(function(){var a=self.$('thumbnails').find('.active');if(!a.length){return false}return a.find('img').attr('src')}()),optval=typeof o.thumbnails==='string'?o.thumbnails.toLowerCase():null,getStyle=function(prop){return doc.defaultView&&doc.defaultView.getComputedStyle?doc.defaultView.getComputedStyle(thumb.container,null)[prop]:$container.css(prop)},fake=function(image,index,container){return function(){$(container).append(image);self.trigger({type:Galleria.THUMBNAIL,thumbTarget:image,index:index})}},onThumbEvent=function(e){if(o.pauseOnInteraction){self.pause()}var index=$(e.currentTarget).data('index');if(self.getIndex()!==index){self.show(index)}e.preventDefault()},onThumbLoad=function(thumb){thumb.scale({width:thumb.data.width,height:thumb.data.height,crop:o.thumbCrop,margin:o.thumbMargin,canvas:o.useCanvas,complete:function(thumb){var top=['left','top'],arr=['Width','Height'],m,css;$.each(arr,function(i,measure){m=measure.toLowerCase();if((o.thumbCrop!==true||o.thumbCrop===m)&&o.thumbFit){css={};css[m]=thumb[m];$(thumb.container).css(css);css={};css[top[i]]=0;$(thumb.image).css(css)}thumb['outer'+measure]=$(thumb.container)['outer'+measure](true)});Utils.toggleQuality(thumb.image,o.thumbQuality===true||(o.thumbQuality==='auto'&&thumb.original.width<thumb.width*3));self.trigger({type:Galleria.THUMBNAIL,thumbTarget:thumb.image,index:thumb.data.order})}})};this._thumbnails=[];this.$('thumbnails').empty();for(i=0;this._data[i];i++){data=this._data[i];if(o.thumbnails===true){thumb=new Galleria.Picture(i);src=data.thumb||data.image;this.$('thumbnails').append(thumb.container);$container=$(thumb.container);thumb.data={width:Utils.parseValue(getStyle('width')),height:Utils.parseValue(getStyle('height')),order:i};if(o.thumbFit&&o.thumbCrop!==true){$container.css({width:0,height:0})}else{$container.css({width:thumb.data.width,height:thumb.data.height})}thumb.load(src,onThumbLoad);if(o.preload==='all'){thumb.preload(data.image)}}else if(optval==='empty'||optval==='numbers'){thumb={container:Utils.create('galleria-image'),image:Utils.create('img','span'),ready:true};if(optval==='numbers'){$(thumb.image).text(i+1)}this.$('thumbnails').append(thumb.container);window.setTimeout((fake)(thumb.image,i,thumb.container),50+(i*20))}else{thumb={container:null,image:null}}$(thumb.container).add(o.keepSource&&o.linkSourceImages?data.original:null).data('index',i).bind(o.thumbEventType,onThumbEvent);if(active===src){$(thumb.container).addClass('active')}this._thumbnails.push(thumb)}},_run:function(){var self=this;self._createThumbnails();Utils.wait({until:function(){if(Galleria.OPERA){self.$('stage').css('display','inline-block')}self._stageWidth=self.$('stage').width();self._stageHeight=self.$('stage').height();return(self._stageWidth&&self._stageHeight>50)},success:function(){_galleries.push(self);Utils.show(self.get('counter'));if(self._options.carousel){self._carousel.bindControls()}if(self._options.autoplay){self.pause();if(typeof self._options.autoplay==='number'){self._playtime=self._options.autoplay}self.trigger(Galleria.PLAY);self._playing=true}if(self._firstrun){if(typeof self._options.show==='number'){self.show(self._options.show)}return}self._firstrun=true;if(Galleria.History){Galleria.History.change(function(value){if(isNaN(value)){window.history.go(-1)}else{self.show(value,undef,true)}})}$.each(Galleria.ready.callbacks,function(){this.call(self,self._options)});self.trigger(Galleria.READY);Galleria.theme.init.call(self,self._options);self._options.extend.call(self,self._options);if(/^[0-9]{1,4}$/.test(HASH)&&Galleria.History){self.show(HASH,undef,true)}else if(self._data[self._options.show]){self.show(self._options.show)}},error:function(){Galleria.raise('Stage width or height is too small to show the gallery. Traced measures: width:'+self._stageWidth+'px, height: '+self._stageHeight+'px.',true)}})},load:function(source,selector,config){var self=this;this._data=[];this._thumbnails=[];this.$('thumbnails').empty();if(typeof selector==='function'){config=selector;selector=null}source=source||this._options.dataSource;selector=selector||this._options.dataSelector;config=config||this._options.dataConfig;if(/^function Object/.test(source.constructor)){source=[source]}if(source.constructor===Array){if(this.validate(source)){this._data=source;this._parseData().trigger(Galleria.DATA)}else{Galleria.raise('Load failed: JSON Array not valid.')}return this}$(source).find(selector).each(function(i,img){img=$(img);var data={},parent=img.parent(),href=parent.attr('href'),rel=parent.attr('rel');if(href){data.image=data.big=href}if(rel){data.big=rel}self._data.push($.extend({title:img.attr('title')||'',thumb:img.attr('src'),image:img.attr('src'),big:img.attr('src'),description:img.attr('alt')||'',link:img.attr('longdesc'),original:img.get(0)},data,config(img)))});if(this.getDataLength()){this.trigger(Galleria.DATA)}else{Galleria.raise('Load failed: no data found.')}return this},_parseData:function(){var self=this;$.each(this._data,function(i,data){if('thumb'in data===false){self._data[i].thumb=data.image}if(!'big'in data){self._data[i].big=data.image}});return this},splice:function(){Array.prototype.splice.apply(this._data,Utils.array(arguments));return this._parseData()._createThumbnails()},push:function(){Array.prototype.push.apply(this._data,Utils.array(arguments));return this._parseData()._createThumbnails()},_getActive:function(){return this._controls.getActive()},validate:function(data){return true},bind:function(type,fn){type=_patchEvent(type);this.$('container').bind(type,this.proxy(fn));return this},unbind:function(type){type=_patchEvent(type);this.$('container').unbind(type);return this},trigger:function(type){type=typeof type==='object'?$.extend(type,{scope:this}):{type:_patchEvent(type),scope:this};this.$('container').trigger(type);return this},addIdleState:function(elem,styles){this._idle.add.apply(this._idle,Utils.array(arguments));return this},removeIdleState:function(elem){this._idle.remove.apply(this._idle,Utils.array(arguments));return this},enterIdleMode:function(){this._idle.hide();return this},exitIdleMode:function(){this._idle.showAll();return this},enterFullscreen:function(callback){this._fullscreen.enter.apply(this,Utils.array(arguments));return this},exitFullscreen:function(callback){this._fullscreen.exit.apply(this,Utils.array(arguments));return this},toggleFullscreen:function(callback){this._fullscreen[this.isFullscreen()?'exit':'enter'].apply(this,Utils.array(arguments));return this},bindTooltip:function(elem,value){this._tooltip.bind.apply(this._tooltip,Utils.array(arguments));return this},defineTooltip:function(elem,value){this._tooltip.define.apply(this._tooltip,Utils.array(arguments));return this},refreshTooltip:function(elem){this._tooltip.show.apply(this._tooltip,Utils.array(arguments));return this},openLightbox:function(){this._lightbox.show.apply(this._lightbox,Utils.array(arguments));return this},closeLightbox:function(){this._lightbox.hide.apply(this._lightbox,Utils.array(arguments));return this},getActiveImage:function(){return this._getActive().image||undef},getActiveThumb:function(){return this._thumbnails[this._active].image||undef},getMousePosition:function(e){return{x:e.pageX-this.$('container').offset().left,y:e.pageY-this.$('container').offset().top}},addPan:function(img){if(this._options.imageCrop===false){return}img=$(img||this.getActiveImage());var self=this,x=img.width()/2,y=img.height()/2,destX=parseInt(img.css('left'),10),destY=parseInt(img.css('top'),10),curX=destX||0,curY=destY||0,distX=0,distY=0,active=false,ts=Utils.timestamp(),cache=0,move=0,position=function(dist,cur,pos){if(dist>0){move=Math.round(Math.max(dist*-1,Math.min(0,cur)));if(cache!==move){cache=move;if(IE===8){img.parent()['scroll'+pos](move*-1)}else{var css={};css[pos.toLowerCase()]=move;img.css(css)}}}},calculate=function(e){if(Utils.timestamp()-ts<50){return}active=true;x=self.getMousePosition(e).x;y=self.getMousePosition(e).y},loop=function(e){if(!active){return}distX=img.width()-self._stageWidth;distY=img.height()-self._stageHeight;destX=x/self._stageWidth*distX*-1;destY=y/self._stageHeight*distY*-1;curX+=(destX-curX)/self._options.imagePanSmoothness;curY+=(destY-curY)/self._options.imagePanSmoothness;position(distY,curY,'Top');position(distX,curX,'Left')};if(IE===8){img.parent().scrollTop(curY*-1).scrollLeft(curX*-1);img.css({top:0,left:0})}this.$('stage').unbind('mousemove',calculate).bind('mousemove',calculate);Utils.addTimer('pan',loop,50,true);return this},proxy:function(fn,scope){if(typeof fn!=='function'){return function(){}}scope=scope||this;return function(){return fn.apply(scope,Utils.array(arguments))}},removePan:function(){this.$('stage').unbind('mousemove');Utils.clearTimer('pan');return this},addElement:function(id){var dom=this._dom;$.each(Utils.array(arguments),function(i,blueprint){dom[blueprint]=Utils.create('galleria-'+blueprint)});return this},attachKeyboard:function(map){this._keyboard.attach.apply(this._keyboard,Utils.array(arguments));return this},detachKeyboard:function(){this._keyboard.detach.apply(this._keyboard,Utils.array(arguments));return this},appendChild:function(parentID,childID){this.$(parentID).append(this.get(childID)||childID);return this},prependChild:function(parentID,childID){this.$(parentID).prepend(this.get(childID)||childID);return this},remove:function(elemID){this.$(Utils.array(arguments).join(',')).remove();return this},append:function(data){var i,j;for(i in data){if(data.hasOwnProperty(i)){if(data[i].constructor===Array){for(j=0;data[i][j];j++){this.appendChild(i,data[i][j])}}else{this.appendChild(i,data[i])}}}return this},_scaleImage:function(image,options){image=image||this._controls.getActive();if(!image){return}var self=this,complete,scaleLayer=function(img){$(img.container).children(':first').css({top:Math.max(0,Utils.parseValue(img.image.style.top)),left:Math.max(0,Utils.parseValue(img.image.style.left)),width:Utils.parseValue(img.image.width),height:Utils.parseValue(img.image.height)})};options=$.extend({width:this._stageWidth,height:this._stageHeight,crop:this._options.imageCrop,max:this._options.maxScaleRatio,min:this._options.minScaleRatio,margin:this._options.imageMargin,position:this._options.imagePosition},options);if(this._options.layerFollow&&this._options.imageCrop!==true){if(typeof options.complete=='function'){complete=options.complete;options.complete=function(){complete.call(image,image);scaleLayer(image)}}else{options.complete=scaleLayer}}else{$(image.container).children(':first').css({top:0,left:0})}image.scale(options);return this},updateCarousel:function(){this._carousel.update();return this},rescale:function(width,height,complete){var self=this;if(typeof width==='function'){complete=width;width=undef}var scale=function(){self._stageWidth=width||self.$('stage').width();self._stageHeight=height||self.$('stage').height();self._scaleImage();if(self._options.carousel){self.updateCarousel()}self.trigger(Galleria.RESCALE);if(typeof complete==='function'){complete.call(self)}};if(Galleria.WEBKIT&&!Galleria.TOUCH&&!width&&!height){Utils.addTimer(false,scale,10)}else{scale.call(self)}return this},refreshImage:function(){this._scaleImage();if(this._options.imagePan){this.addPan()}return this},show:function(index,rewind,_history){if(index===false||(!this._options.queue&&this._queue.stalled)){return}index=Math.max(0,Math.min(parseInt(index,10),this.getDataLength()-1));rewind=typeof rewind!=='undefined'?!!rewind:index<this.getIndex();_history=_history||false;if(!_history&&Galleria.History){Galleria.History.set(index.toString());return}this._active=index;Array.prototype.push.call(this._queue,{index:index,rewind:rewind});if(!this._queue.stalled){this._show()}return this},_show:function(){var self=this,queue=this._queue[0],data=this.getData(queue.index);if(!data){return}var src=this.isFullscreen()&&'big'in data?data.big:data.image,active=this._controls.getActive(),next=this._controls.getNext(),cached=next.isCached(src),thumb=this._thumbnails[queue.index],mousetrigger=function(){$(next.image).trigger('mouseup')};var complete=(function(data,next,active,queue,thumb){return function(){var win;self._queue.stalled=false;Utils.toggleQuality(next.image,self._options.imageQuality);self._layers[self._controls.active].innerHTML='';$(active.container).css({zIndex:0,opacity:0}).show();$(next.container).css({zIndex:1}).show();self._controls.swap();if(self._options.imagePan){self.addPan(next.image)}if(data.link||self._options.lightbox||self._options.clicknext){$(next.image).css({cursor:'pointer'}).bind('mouseup',function(){if(self._options.clicknext&&!Galleria.TOUCH){if(self._options.pauseOnInteraction){self.pause()}self.next();return}if(data.link){if(self._options.popupLinks){win=window.open(data.link,'_blank')}else{window.location.href=data.link}return}self.openLightbox()})}Array.prototype.shift.call(self._queue);if(self._queue.length){self._show()}self._playCheck();self.trigger({type:Galleria.IMAGE,index:queue.index,imageTarget:next.image,thumbTarget:thumb.image})}}(data,next,active,queue,thumb));if(this._options.carousel&&this._options.carouselFollow){this._carousel.follow(queue.index)}if(this._options.preload){var p,i,n=this.getNext(),ndata;try{for(i=this._options.preload;i>0;i--){p=new Galleria.Picture();ndata=self.getData(n);p.preload(this.isFullscreen()&&'big'in ndata?ndata.big:ndata.image);n=self.getNext(n)}}catch(e){}}Utils.show(next.container);$(self._thumbnails[queue.index].container).addClass('active').siblings('.active').removeClass('active');self.trigger({type:Galleria.LOADSTART,cached:cached,index:queue.index,rewind:queue.rewind,imageTarget:next.image,thumbTarget:thumb.image});next.load(src,function(next){var layer=$(self._layers[1-self._controls.active]).html(data.layer||'').hide();self._scaleImage(next,{complete:function(next){if('image'in active){Utils.toggleQuality(active.image,false)}Utils.toggleQuality(next.image,false);self._queue.stalled=true;self.removePan();self.setInfo(queue.index);self.setCounter(queue.index);if(data.layer){layer.show();if(data.link||self._options.lightbox||self._options.clicknext){layer.css('cursor','pointer').unbind('mouseup').mouseup(mousetrigger)}}self.trigger({type:Galleria.LOADFINISH,cached:cached,index:queue.index,rewind:queue.rewind,imageTarget:next.image,thumbTarget:self._thumbnails[queue.index].image});var transition=self._options.transition;$.each({initial:active.image===null,touch:Galleria.TOUCH,fullscreen:self.isFullscreen()},function(type,arg){if(arg&&self._options[type+'Transition']!==undef){transition=self._options[type+'Transition'];return false}});if(transition in _transitions===false){complete()}else{var params={prev:active.container,next:next.container,rewind:queue.rewind,speed:self._options.transitionSpeed||400};_transitions[transition].call(self,params,complete)}}})})},getNext:function(base){base=typeof base==='number'?base:this.getIndex();return base===this.getDataLength()-1?0:base+1},getPrev:function(base){base=typeof base==='number'?base:this.getIndex();return base===0?this.getDataLength()-1:base-1},next:function(){if(this.getDataLength()>1){this.show(this.getNext(),false)}return this},prev:function(){if(this.getDataLength()>1){this.show(this.getPrev(),true)}return this},get:function(elemId){return elemId in this._dom?this._dom[elemId]:null},getData:function(index){return index in this._data?this._data[index]:this._data[this._active]},getDataLength:function(){return this._data.length},getIndex:function(){return typeof this._active==='number'?this._active:false},getStageHeight:function(){return this._stageHeight},getStageWidth:function(){return this._stageWidth},getOptions:function(key){return typeof key==='undefined'?this._options:this._options[key]},setOptions:function(key,value){if(typeof key==='object'){$.extend(this._options,key)}else{this._options[key]=value}return this},play:function(delay){this._playing=true;this._playtime=delay||this._playtime;this._playCheck();this.trigger(Galleria.PLAY);return this},pause:function(){this._playing=false;this.trigger(Galleria.PAUSE);return this},playToggle:function(delay){return(this._playing)?this.pause():this.play(delay)},isPlaying:function(){return this._playing},isFullscreen:function(){return this._fullscreen.active},_playCheck:function(){var self=this,played=0,interval=20,now=Utils.timestamp(),timer_id='play'+this._id;if(this._playing){Utils.clearTimer(timer_id);var fn=function(){played=Utils.timestamp()-now;if(played>=self._playtime&&self._playing){Utils.clearTimer(timer_id);self.next();return}if(self._playing){self.trigger({type:Galleria.PROGRESS,percent:Math.ceil(played/self._playtime*100),seconds:Math.floor(played/1000),milliseconds:played});Utils.addTimer(timer_id,fn,interval)}};Utils.addTimer(timer_id,fn,interval)}},setPlaytime:function(delay){this._playtime=delay;return this},setIndex:function(val){this._active=val;return this},setCounter:function(index){if(typeof index==='number'){index++}else if(typeof index==='undefined'){index=this.getIndex()+1}this.get('current').innerHTML=index;if(IE){var count=this.$('counter'),opacity=count.css('opacity');if(parseInt(opacity,10)===1){Utils.removeAlpha(count[0])}else{this.$('counter').css('opacity',opacity)}}return this},setInfo:function(index){var self=this,data=this.getData(index);$.each(['title','description'],function(i,type){var elem=self.$('info-'+type);if(!!data[type]){elem[data[type].length?'show':'hide']().html(data[type])}else{elem.empty().hide()}});return this},hasInfo:function(index){var check='title description'.split(' '),i;for(i=0;check[i];i++){if(!!this.getData(index)[check[i]]){return true}}return false},jQuery:function(str){var self=this,ret=[];$.each(str.split(','),function(i,elemId){elemId=$.trim(elemId);if(self.get(elemId)){ret.push(elemId)}});var jQ=$(self.get(ret.shift()));$.each(ret,function(i,elemId){jQ=jQ.add(self.get(elemId))});return jQ},$:function(str){return this.jQuery.apply(this,Utils.array(arguments))}};$.each(_events,function(i,ev){var type=/_/.test(ev)?ev.replace(/_/g,''):ev;Galleria[ev.toUpperCase()]='galleria.'+type});$.extend(Galleria,{IE9:IE===9,IE8:IE===8,IE7:IE===7,IE6:IE===6,IE:IE,WEBKIT:/webkit/.test(NAV),SAFARI:/safari/.test(NAV),CHROME:/chrome/.test(NAV),QUIRK:(IE&&doc.compatMode&&doc.compatMode==="BackCompat"),MAC:/mac/.test(navigator.platform.toLowerCase()),OPERA:!!window.opera,IPHONE:/iphone/.test(NAV),IPAD:/ipad/.test(NAV),ANDROID:/android/.test(NAV),TOUCH:('ontouchstart'in doc)});Galleria.addTheme=function(theme){if(!theme.name){Galleria.raise('No theme name specified')}if(typeof theme.defaults!=='object'){theme.defaults={}}else{theme.defaults=_legacyOptions(theme.defaults)}var css=false,reg;if(typeof theme.css==='string'){$('link').each(function(i,link){reg=new RegExp(theme.css);if(reg.test(link.href)){css=true;_themeLoad(theme);return false}});if(!css){$('script').each(function(i,script){reg=new RegExp('galleria\\.'+theme.name.toLowerCase()+'\\.');if(reg.test(script.src)){css=script.src.replace(/[^\/]*$/,'')+theme.css;Utils.addTimer("css",function(){Utils.loadCSS(css,'galleria-theme',function(){_themeLoad(theme)})},1)}})}if(!css){Galleria.raise('No theme CSS loaded')}}else{_themeLoad(theme)}return theme};Galleria.loadTheme=function(src,options){var loaded=false,length=_galleries.length,err=window.setTimeout(function(){Galleria.raise("Theme at "+src+" could not load, check theme path.",true)},5000);Galleria.theme=undef;Utils.loadScript(src,function(){window.clearTimeout(err);if(length){var refreshed=[];$.each(Galleria.get(),function(i,instance){var op=$.extend(instance._original.options,{data_source:instance._data},options);instance.$('container').remove();var g=new Galleria();g._id=instance._id;g.init(instance._original.target,op);refreshed.push(g)});_galleries=refreshed}})};Galleria.get=function(index){if(!!_instances[index]){return _instances[index]}else if(typeof index!=='number'){return _instances}else{Galleria.raise('Gallery index '+index+' not found')}};Galleria.addTransition=function(name,fn){_transitions[name]=fn};Galleria.utils=Utils;Galleria.log=(function(){if('console'in window&&'log'in window.console){return window.console.log}else{return function(){window.alert(Utils.array(arguments).join(', '))}}}());Galleria.ready=function(fn){$.each(_galleries,function(i,gallery){fn.call(gallery,gallery._options)});Galleria.ready.callbacks.push(fn)};Galleria.ready.callbacks=[];Galleria.raise=function(msg,fatal){var type=fatal?'Fatal error':'Error',self=this,echo=function(msg){var html='<div style="padding:4px;margin:0 0 2px;background:#'+(fatal?'811':'222')+'";>'+(fatal?'<strong>'+type+': </strong>':'')+msg+'</div>';$.each(_instances,function(){var cont=this.$('errors'),target=this.$('target');if(!cont.length){target.css('position','relative');cont=this.addElement('errors').appendChild('target','errors').$('errors').css({color:'#fff',position:'absolute',top:0,left:0,zIndex:100000})}cont.append(html)})};if(DEBUG){echo(msg);if(fatal){throw new Error(type+': '+msg)}}else if(fatal){if(_hasError){return}_hasError=true;fatal=false;echo('Image gallery could not load.')}};Galleria.version=VERSION;Galleria.requires=function(version,msg){msg=msg||'You need to upgrade Galleria to version '+version+' to use one or more components.';if(Galleria.version<version){Galleria.raise(msg,true)}};Galleria.Picture=function(id){this.id=id||null;this.image=null;this.container=Utils.create('galleria-image');$(this.container).css({overflow:'hidden',position:'relative'});this.original={width:0,height:0};this.ready=false;this.tid=null};Galleria.Picture.prototype={cache:{},show:function(){Utils.show(this.image)},hide:function(){Utils.moveOut(this.image)},clear:function(){this.image=null},isCached:function(src){return!!this.cache[src]},preload:function(src){$(new Image()).load((function(src,cache){return function(){cache[src]=src}}(src,this.cache))).attr('src',src)},load:function(src,callback){this.tid=window.setTimeout((function(src){return function(){Galleria.raise('Image not loaded in '+Math.round(TIMEOUT/1000)+' seconds: '+src)}}(src)),TIMEOUT);this.image=new Image();var i=0,reload=false,$container=$(this.container),$image=$(this.image),onload=(function(self,callback,src){return function(){var complete=function(){$(this).unbind('load');self.original={height:this.height,width:this.width};self.cache[src]=src;window.clearTimeout(self.tid);if(typeof callback=='function'){window.setTimeout(function(){callback.call(self,self)},1)}};if((!this.width||!this.height)){window.setTimeout((function(img){return function(){if(img.width&&img.height){complete.call(img)}else{Galleria.raise('Could not extract width/height from image: '+img.src+'. Traced measures: width:'+img.width+'px, height: '+img.height+'px.')}}}(this)),2)}else{complete.call(this)}}}(this,callback,src));$container.find('img').remove();$image.css('display','block').appendTo(this.container);Utils.hide(this.image);if(this.cache[src]){$(this.image).load(onload).attr('src',src);return this.container}$(this.image).load(onload).error(function(){if(!reload){reload=true;window.setTimeout((function(image,src){return function(){image.attr('src',src+'?'+Utils.timestamp())}}($(this),src)),50)}else{if(DUMMY){$(this).attr('src',DUMMY)}else{Galleria.raise('Image not found: '+src)}}}).attr('src',src);return this.container},scale:function(options){options=$.extend({width:0,height:0,min:undef,max:undef,margin:0,complete:function(){},position:'center',crop:false,canvas:false},options);if(!this.image){return this.container}var width,height,self=this,$container=$(self.container),data;Utils.wait({until:function(){width=options.width||$container.width()||Utils.parseValue($container.css('width'));height=options.height||$container.height()||Utils.parseValue($container.css('height'));return width&&height},success:function(){var newWidth=(width-options.margin*2)/self.original.width,newHeight=(height-options.margin*2)/self.original.height,cropMap={'true':Math.max(newWidth,newHeight),'width':newWidth,'height':newHeight,'false':Math.min(newWidth,newHeight)},ratio=cropMap[options.crop.toString()],canvasKey='';if(options.max){ratio=Math.min(options.max,ratio)}if(options.min){ratio=Math.max(options.min,ratio)}$.each(['width','height'],function(i,m){$(self.image)[m](self[m]=self.image[m]=Math.round(self.original[m]*ratio))});$(self.container).width(width).height(height);if(options.canvas&&_canvas){_canvas.elem.width=self.width;_canvas.elem.height=self.height;canvasKey=self.image.src+':'+self.width+'x'+self.height;self.image.src=_canvas.cache[canvasKey]||(function(key){_canvas.context.drawImage(self.image,0,0,self.original.width*ratio,self.original.height*ratio);try{data=_canvas.elem.toDataURL();_canvas.length+=data.length;_canvas.cache[key]=data;return data}catch(e){return self.image.src}}(canvasKey))}var pos={},mix={},getPosition=function(value,measure,margin){var result=0;if(/\%/.test(value)){var flt=parseInt(value,10)/100,m=self.image[measure]||$(self.image)[measure]();result=Math.ceil(m*-1*flt+margin*flt)}else{result=Utils.parseValue(value)}return result},positionMap={'top':{top:0},'left':{left:0},'right':{left:'100%'},'bottom':{top:'100%'}};$.each(options.position.toLowerCase().split(' '),function(i,value){if(value==='center'){value='50%'}pos[i?'top':'left']=value});$.each(pos,function(i,value){if(positionMap.hasOwnProperty(value)){$.extend(mix,positionMap[value])}});pos=pos.top?$.extend(pos,mix):mix;pos=$.extend({top:'50%',left:'50%'},pos);$(self.image).css({position:'absolute',top:getPosition(pos.top,'height',height),left:getPosition(pos.left,'width',width)});self.show();self.ready=true;options.complete.call(self,self)},error:function(){Galleria.raise('Could not scale image: '+self.image.src)},timeout:1000});return this}};$.extend($.easing,{galleria:function(_,t,b,c,d){if((t/=d/2)<1){return c/2*t*t*t+b}return c/2*((t-=2)*t*t+2)+b},galleriaIn:function(_,t,b,c,d){return c*(t/=d)*t+b},galleriaOut:function(_,t,b,c,d){return-c*(t/=d)*(t-2)+b}});$.fn.galleria=function(options){return this.each(function(){$(this).data('galleria',new Galleria().init(this,options))})}}(jQuery));



/*
 * SimpleModal 1.4.2 - jQuery Plugin
 * http://simplemodal.com/
 * Copyright (c) 2011 Eric Martin
 * Licensed under MIT and GPL
 * Date: Sat, Dec 17 2011 15:35:38 -0800
 */
(function(b){"function"===typeof define&&define.amd?define(["jquery"],b):b(jQuery)})(function(b){var j=[],k=b(document),l=b.browser.msie&&6===parseInt(b.browser.version)&&"object"!==typeof window.XMLHttpRequest,n=b.browser.msie&&7===parseInt(b.browser.version),m=null,h=b(window),i=[];b.modal=function(a,d){return b.modal.impl.init(a,d)};b.modal.close=function(){b.modal.impl.close()};b.modal.focus=function(a){b.modal.impl.focus(a)};b.modal.setContainerDimensions=function(){b.modal.impl.setContainerDimensions()};
b.modal.setPosition=function(){b.modal.impl.setPosition()};b.modal.update=function(a,d){b.modal.impl.update(a,d)};b.fn.modal=function(a){return b.modal.impl.init(this,a)};b.modal.defaults={appendTo:"body",focus:!0,opacity:50,overlayId:"simplemodal-overlay",overlayCss:{},containerId:"simplemodal-container",containerCss:{},dataId:"simplemodal-data",dataCss:{},minHeight:null,minWidth:null,maxHeight:null,maxWidth:null,autoResize:!1,autoPosition:!0,zIndex:1E3,close:!0,closeHTML:'<a class="modalCloseImg" title="Close"></a>',
closeClass:"simplemodal-close",escClose:!0,overlayClose:!1,fixed:!0,position:null,persist:!1,modal:!0,onOpen:null,onShow:null,onClose:null};b.modal.impl={d:{},init:function(a,d){if(this.d.data)return!1;m=b.browser.msie&&!b.boxModel;this.o=b.extend({},b.modal.defaults,d);this.zIndex=this.o.zIndex;this.occb=!1;if("object"===typeof a){if(a=a instanceof jQuery?a:b(a),this.d.placeholder=!1,0<a.parent().parent().size()&&(a.before(b("<span></span>").attr("id","simplemodal-placeholder").css({display:"none"})),
this.d.placeholder=!0,this.display=a.css("display"),!this.o.persist))this.d.orig=a.clone(!0)}else if("string"===typeof a||"number"===typeof a)a=b("<div></div>").html(a);else return alert("SimpleModal Error: Unsupported data type: "+typeof a),this;this.create(a);this.open();b.isFunction(this.o.onShow)&&this.o.onShow.apply(this,[this.d]);return this},create:function(a){this.getDimensions();if(this.o.modal&&l)this.d.iframe=b('<iframe src="javascript:false;"></iframe>').css(b.extend(this.o.iframeCss,
{display:"none",opacity:0,position:"fixed",height:i[0],width:i[1],zIndex:this.o.zIndex,top:0,left:0})).appendTo(this.o.appendTo);this.d.overlay=b("<div></div>").attr("id",this.o.overlayId).addClass("simplemodal-overlay").css(b.extend(this.o.overlayCss,{display:"none",opacity:this.o.opacity/100,height:this.o.modal?j[0]:0,width:this.o.modal?j[1]:0,position:"fixed",left:0,top:0,zIndex:this.o.zIndex+1})).appendTo(this.o.appendTo);this.d.container=b("<div></div>").attr("id",this.o.containerId).addClass("simplemodal-container").css(b.extend({position:this.o.fixed?
"fixed":"absolute"},this.o.containerCss,{display:"none",zIndex:this.o.zIndex+2})).append(this.o.close&&this.o.closeHTML?b(this.o.closeHTML).addClass(this.o.closeClass):"").appendTo(this.o.appendTo);this.d.wrap=b("<div></div>").attr("tabIndex",-1).addClass("simplemodal-wrap").css({height:"100%",outline:0,width:"100%"}).appendTo(this.d.container);this.d.data=a.attr("id",a.attr("id")||this.o.dataId).addClass("simplemodal-data").css(b.extend(this.o.dataCss,{display:"none"})).appendTo("body");this.setContainerDimensions();
this.d.data.appendTo(this.d.wrap);(l||m)&&this.fixIE()},bindEvents:function(){var a=this;b("."+a.o.closeClass).bind("click.simplemodal",function(b){b.preventDefault();a.close()});a.o.modal&&a.o.close&&a.o.overlayClose&&a.d.overlay.bind("click.simplemodal",function(b){b.preventDefault();a.close()});k.bind("keydown.simplemodal",function(b){a.o.modal&&9===b.keyCode?a.watchTab(b):a.o.close&&a.o.escClose&&27===b.keyCode&&(b.preventDefault(),a.close())});h.bind("resize.simplemodal orientationchange.simplemodal",
function(){a.getDimensions();a.o.autoResize?a.setContainerDimensions():a.o.autoPosition&&a.setPosition();l||m?a.fixIE():a.o.modal&&(a.d.iframe&&a.d.iframe.css({height:i[0],width:i[1]}),a.d.overlay.css({height:j[0],width:j[1]}))})},unbindEvents:function(){b("."+this.o.closeClass).unbind("click.simplemodal");k.unbind("keydown.simplemodal");h.unbind(".simplemodal");this.d.overlay.unbind("click.simplemodal")},fixIE:function(){var a=this.o.position;b.each([this.d.iframe||null,!this.o.modal?null:this.d.overlay,
"fixed"===this.d.container.css("position")?this.d.container:null],function(b,f){if(f){var g=f[0].style;g.position="absolute";if(2>b)g.removeExpression("height"),g.removeExpression("width"),g.setExpression("height",'document.body.scrollHeight > document.body.clientHeight ? document.body.scrollHeight : document.body.clientHeight + "px"'),g.setExpression("width",'document.body.scrollWidth > document.body.clientWidth ? document.body.scrollWidth : document.body.clientWidth + "px"');else{var c,e;a&&a.constructor===
Array?(c=a[0]?"number"===typeof a[0]?a[0].toString():a[0].replace(/px/,""):f.css("top").replace(/px/,""),c=-1===c.indexOf("%")?c+' + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"':parseInt(c.replace(/%/,""))+' * ((document.documentElement.clientHeight || document.body.clientHeight) / 100) + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"',a[1]&&(e="number"===typeof a[1]?
a[1].toString():a[1].replace(/px/,""),e=-1===e.indexOf("%")?e+' + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"':parseInt(e.replace(/%/,""))+' * ((document.documentElement.clientWidth || document.body.clientWidth) / 100) + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"')):(c='(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"',
e='(document.documentElement.clientWidth || document.body.clientWidth) / 2 - (this.offsetWidth / 2) + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"');g.removeExpression("top");g.removeExpression("left");g.setExpression("top",c);g.setExpression("left",e)}}})},focus:function(a){var d=this,a=a&&-1!==b.inArray(a,["first","last"])?a:"first",f=b(":input:enabled:visible:"+a,d.d.wrap);setTimeout(function(){0<f.length?f.focus():d.d.wrap.focus()},
10)},getDimensions:function(){var a=b.browser.opera&&"9.5"<b.browser.version&&"1.3">b.fn.jquery||b.browser.opera&&"9.5">b.browser.version&&"1.2.6"<b.fn.jquery?h[0].innerHeight:h.height();j=[k.height(),k.width()];i=[a,h.width()]},getVal:function(a,b){return a?"number"===typeof a?a:"auto"===a?0:0<a.indexOf("%")?parseInt(a.replace(/%/,""))/100*("h"===b?i[0]:i[1]):parseInt(a.replace(/px/,"")):null},update:function(a,b){if(!this.d.data)return!1;this.d.origHeight=this.getVal(a,"h");this.d.origWidth=this.getVal(b,
"w");this.d.data.hide();a&&this.d.container.css("height",a);b&&this.d.container.css("width",b);this.setContainerDimensions();this.d.data.show();this.o.focus&&this.focus();this.unbindEvents();this.bindEvents()},setContainerDimensions:function(){var a=l||n,d=this.d.origHeight?this.d.origHeight:b.browser.opera?this.d.container.height():this.getVal(a?this.d.container[0].currentStyle.height:this.d.container.css("height"),"h"),a=this.d.origWidth?this.d.origWidth:b.browser.opera?this.d.container.width():
this.getVal(a?this.d.container[0].currentStyle.width:this.d.container.css("width"),"w"),f=this.d.data.outerHeight(!0),g=this.d.data.outerWidth(!0);this.d.origHeight=this.d.origHeight||d;this.d.origWidth=this.d.origWidth||a;var c=this.o.maxHeight?this.getVal(this.o.maxHeight,"h"):null,e=this.o.maxWidth?this.getVal(this.o.maxWidth,"w"):null,c=c&&c<i[0]?c:i[0],e=e&&e<i[1]?e:i[1],h=this.o.minHeight?this.getVal(this.o.minHeight,"h"):"auto",d=d?this.o.autoResize&&d>c?c:d<h?h:d:f?f>c?c:this.o.minHeight&&
"auto"!==h&&f<h?h:f:h,c=this.o.minWidth?this.getVal(this.o.minWidth,"w"):"auto",a=a?this.o.autoResize&&a>e?e:a<c?c:a:g?g>e?e:this.o.minWidth&&"auto"!==c&&g<c?c:g:c;this.d.container.css({height:d,width:a});this.d.wrap.css({overflow:f>d||g>a?"auto":"visible"});this.o.autoPosition&&this.setPosition()},setPosition:function(){var a,b;a=i[0]/2-this.d.container.outerHeight(!0)/2;b=i[1]/2-this.d.container.outerWidth(!0)/2;var f="fixed"!==this.d.container.css("position")?h.scrollTop():0;this.o.position&&"[object Array]"===
Object.prototype.toString.call(this.o.position)?(a=f+(this.o.position[0]||a),b=this.o.position[1]||b):a=f+a;this.d.container.css({left:b,top:a})},watchTab:function(a){if(0<b(a.target).parents(".simplemodal-container").length){if(this.inputs=b(":input:enabled:visible:first, :input:enabled:visible:last",this.d.data[0]),!a.shiftKey&&a.target===this.inputs[this.inputs.length-1]||a.shiftKey&&a.target===this.inputs[0]||0===this.inputs.length)a.preventDefault(),this.focus(a.shiftKey?"last":"first")}else a.preventDefault(),
this.focus()},open:function(){this.d.iframe&&this.d.iframe.show();b.isFunction(this.o.onOpen)?this.o.onOpen.apply(this,[this.d]):(this.d.overlay.show(),this.d.container.show(),this.d.data.show());this.o.focus&&this.focus();this.bindEvents()},close:function(){if(!this.d.data)return!1;this.unbindEvents();if(b.isFunction(this.o.onClose)&&!this.occb)this.occb=!0,this.o.onClose.apply(this,[this.d]);else{if(this.d.placeholder){var a=b("#simplemodal-placeholder");this.o.persist?a.replaceWith(this.d.data.removeClass("simplemodal-data").css("display",
this.display)):(this.d.data.hide().remove(),a.replaceWith(this.d.orig))}else this.d.data.hide().remove();this.d.container.hide().remove();this.d.overlay.hide();this.d.iframe&&this.d.iframe.hide().remove();this.d.overlay.remove();this.d={}}}}});


/*
 * SimpleModal Basic Modal Dialog
 * http://www.ericmmartin.com/projects/simplemodal/
 * http://code.google.com/p/simplemodal/
 *
 * Copyright (c) 2009 Eric Martin - http://ericmmartin.com
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Revision: $Id: basic.js 212 2009-09-03 05:33:44Z emartin24 $
 *
 */

jQuery(document).ready(function () {
	jQuery('a.b_sendtofriend').click(function (e) {
		e.preventDefault();
		jQuery('#basic-modal-content').modal({persist:true});
	});
	
	jQuery('a.b_claim_listing').click(function (e) {
		e.preventDefault();
		jQuery('#basic-modal-content4').modal({persist:true});
	});
	
		jQuery('a.b_send_inquiry' ).click(function (e) {
		e.preventDefault();
		jQuery('#basic-modal-content2').modal({persist:true});
	});
		
		jQuery('p.links a.a_image_sort').click(function (e) {
		e.preventDefault();
		jQuery('#basic-modal-content3').modal({persist:true});
	});
	
});


jQuery(document).ready(function(){
//global vars
	var enquiryfrm = jQuery("#send_to_frnd");
	var to_name = jQuery("#to_name");
	var to_nameInfo = jQuery("#to_nameInfo");
	var to_email = jQuery("#to_email");
	var to_emailInfo = jQuery("#to_emailInfo");
	var yourname = jQuery("#yourname");
	var yournameInfo = jQuery("#yournameInfo");
	var youremail = jQuery("#youremail");
	var youremailInfo = jQuery("#youremailInfo");
	var frnd_comments = jQuery("#frnd_comments");
	var frnd_commentsInfo = jQuery("#frnd_commentsInfo");
	
	var frnd_subject = jQuery("#frnd_subject");
	var frnd_subjectInfo = jQuery("#frnd_subjectInfo");

	//On blur
	to_name.blur(validate_to_name);
	to_email.blur(validate_to_email);
	yourname.blur(validate_yourname);
	youremail.blur(validate_youremail);
	frnd_comments.blur(validate_frnd_comments);
	frnd_subject.blur(validate_frnd_subject);

	//On key press
	to_name.keyup(validate_to_name);
	to_email.keyup(validate_to_email);
	yourname.keyup(validate_yourname);
	youremail.keyup(validate_youremail);
	frnd_comments.keyup(validate_frnd_comments);
	frnd_subject.keyup(validate_frnd_subject);

	//On Submitting
	enquiryfrm.submit(function(){
		if(validate_to_name() & validate_to_email() & validate_yourname() & validate_youremail() & validate_frnd_subject() & validate_frnd_comments())
		{
			function reset_send_email_agent_form()
			{
				document.getElementById('to_name').value = '';
				document.getElementById('to_email').value = '';
				document.getElementById('yourname').value = '';
				document.getElementById('youremail').value = '';	
				document.getElementById('frnd_subject').value = '';
				document.getElementById('frnd_comments').value = '';	
			}
			return true
		}
		else
		{
			return false;
		}
	});
	
	//validation functions
	function validate_to_name()
	{
		if(jQuery("#to_name").val() == '')
		{
			to_name.addClass("error");
			to_nameInfo.text("Please Enter To Name");
			to_nameInfo.addClass("message_error2");
			return false;
		}
		else{
			to_name.removeClass("error");
			to_nameInfo.text("");
			to_nameInfo.removeClass("message_error2");
			return true;
		}
	}
	function validate_to_email()
	{
		var isvalidemailflag = 0;
		if(jQuery("#to_email").val() == '')
		{
			isvalidemailflag = 1;
		}else
		if(jQuery("#to_email").val() != '')
		{
			var a = jQuery("#to_email").val();
			var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
			//if it's valid email
			if(filter.test(a)){
				isvalidemailflag = 0;
			}else{
				isvalidemailflag = 1;	
			}
		}
		if(isvalidemailflag)
		{
			to_email.addClass("error");
			to_emailInfo.text("Please Enter valid Email Address");
			to_emailInfo.addClass("message_error2");
			return false;
		}else
		{
			to_email.removeClass("error");
			to_emailInfo.text("");
			to_emailInfo.removeClass("message_error");
			return true;
		}
	}

	function validate_yourname()
	{
		if(jQuery("#yourname").val() == '')
		{
			yourname.addClass("error");
			yournameInfo.text("Please Enter Your Name");
			yournameInfo.addClass("message_error2");
			return false;
		}
		else{
			yourname.removeClass("error");
			yournameInfo.text("");
			yournameInfo.removeClass("message_error2");
			return true;
		}
	}

	function validate_youremail()
	{
		var isvalidemailflag = 0;
		if(jQuery("#youremail").val() == '')
		{
			isvalidemailflag = 1;
		}else
		if(jQuery("#youremail").val() != '')
		{
			var a = jQuery("#youremail").val();
			var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
			//if it's valid email
			if(filter.test(a)){
				isvalidemailflag = 0;
			}else{
				isvalidemailflag = 1;	
			}
		}
		if(isvalidemailflag)
		{
			youremail.addClass("error");
			youremailInfo.text("Please Enter valid Email Address");
			youremailInfo.addClass("message_error2");
			return false;
		}else
		{
			youremail.removeClass("error");
			youremailInfo.text("");
			youremailInfo.removeClass("message_error");
			return true;
		}
	}
	function validate_frnd_comments()

	{
		if(jQuery("#frnd_comments").val() == '')
		{
			frnd_comments.addClass("error");
			frnd_commentsInfo.text("Please Enter Comments");
			frnd_commentsInfo.addClass("message_error2");
			return false;
		}
		else{
			frnd_comments.removeClass("error");
			frnd_commentsInfo.text("");
			frnd_commentsInfo.removeClass("message_error2");
			return true;
		}
	}
	
	function validate_frnd_subject()
	{
		if(jQuery("#frnd_subject").val() == '')
		{
			frnd_subject.addClass("error");
			frnd_subjectInfo.text("Please Enter Subject");
			frnd_subjectInfo.addClass("message_error2");
			return false;
		}
		else{
			frnd_subject.removeClass("error");
			frnd_subjectInfo.text("");
			frnd_subjectInfo.removeClass("message_error2");
			return true;
		}
	}
});










jQuery(document).ready(function(){

//global vars

	var enquiryfrm = jQuery("#agt_mail_agent");

	var yourname = jQuery("#agt_mail_name");

	var yournameInfo = jQuery("#span_agt_mail_name");

	var youremail = jQuery("#agt_mail_email");

	var youremailInfo = jQuery("#span_agt_mail_email");

	var frnd_comments = jQuery("#agt_mail_msg");

	var frnd_commentsInfo = jQuery("#span_agt_mail_msg");

	

	//On blur

	yourname.blur(validate_yourname);

	youremail.blur(validate_youremail);

	frnd_comments.blur(validate_frnd_comments_author);

	//On key press

	yourname.keyup(validate_yourname);

	youremail.keyup(validate_youremail);

	frnd_comments.keyup(validate_frnd_comments_author);

	

	//On Submitting

	enquiryfrm.submit(function(){

		if(validate_yourname() & validate_youremail() & validate_frnd_comments_author())

		{
			//hideform();
			return true
		}
		else
		{
			return false;
		}
	});



	//validation functions

	function validate_yourname()

	{

		if(jQuery("#agt_mail_name").val() == '')

		{

			yourname.addClass("error");

			yournameInfo.text("Please Enter Your Name");

			yournameInfo.addClass("message_error2");

			return false;

		}

		else{

			yourname.removeClass("error");

			yournameInfo.text("");

			yournameInfo.removeClass("message_error2");

			return true;

		}

	}

	function validate_youremail()

	{

		var isvalidemailflag = 0;

		if(jQuery("#agt_mail_email").val() == '')

		{

			isvalidemailflag = 1;

		}else

		if(jQuery("#agt_mail_email").val() != '')

		{

			var a = jQuery("#agt_mail_email").val();

			var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;

			//if it's valid email

			if(filter.test(a)){

				isvalidemailflag = 0;

			}else{

				isvalidemailflag = 1;	

			}

		}

		if(isvalidemailflag)

		{

			youremail.addClass("error");

			youremailInfo.text("Please Enter valid Email Address");

			youremailInfo.addClass("message_error2");

			return false;

		}else

		{

			youremail.removeClass("error");

			youremailInfo.text("");

			youremailInfo.removeClass("message_error");

			return true;

		}

		

	}

	

	function validate_frnd_comments_author()
	{				
		if(jQuery("#agt_mail_msg").val() == '')
		{
			frnd_comments.addClass("error");
			frnd_commentsInfo.text("Please Enter Comments");
			frnd_commentsInfo.addClass("message_error2");
			return false;
		}else{
			frnd_comments.removeClass("error");
			frnd_commentsInfo.text("");
			frnd_commentsInfo.removeClass("message_error2");
			return true;
		}

	}	
function reset_email_agent_form()
{
	document.getElementById('agt_mail_name').value = '';
	document.getElementById('agt_mail_email').value = '';
	document.getElementById('agt_mail_phone').value = '';
	document.getElementById('agt_mail_msg').value = '';	
}
});





jQuery(document).ready(function(){
//global vars
	var enquiryfrm = jQuery("#claim_form");
	var full_name = jQuery("#full_name");
	var full_nameInfo = jQuery("#full_nameInfo");
	var user_number = jQuery("#user_number");
	var user_numberInfo = jQuery("#user_numberInfo");
	var user_position = jQuery("#user_position");
	var user_positionInfo = jQuery("#user_positionInfo");
	var user_comments = jQuery("#user_comments");
	var user_commentsInfo = jQuery("#user_commentsInfo");

	//On blur
	full_name.blur(validate_full_name);
	user_number.blur(validate_user_number);
	user_position.blur(validate_user_position);
	user_comments.blur(validate_user_comments);

	//On key press
	full_name.keyup(validate_full_name);
	user_number.keyup(validate_user_number);
	user_position.keyup(validate_user_position);
	user_comments.keyup(validate_user_comments);

	//On Submitting
	enquiryfrm.submit(function(){
		if(validate_full_name() & validate_user_number() & validate_user_position() & validate_user_comments())
		{
			function reset_send_email_agent_form()
			{
				document.getElementById('full_name').value = '';
				document.getElementById('user_number').value = '';
				document.getElementById('user_position').value = '';
				document.getElementById('user_comments').value = '';	
				
			}
			return true
		}
		else
		{
			return false;
		}
	});
	
	//validation functions
	function validate_full_name()
	{
		if(jQuery("#full_name").val() == '')
		{
			full_name.addClass("error");
			full_nameInfo.text("Please Enter Your Full Name");
			full_nameInfo.addClass("message_error2");
			return false;
		}
		else{
			full_name.removeClass("error");
			full_nameInfo.text("");
			full_nameInfo.removeClass("message_error2");
			return true;
		}
	}
		function validate_user_number()
	{
		if(jQuery("#user_number").val() == '')
		{
			user_number.addClass("error");
			user_numberInfo.text("Please Enter A Valid Contact Number");
			user_numberInfo.addClass("message_error2");
			return false;
		}
		else{
			user_number.removeClass("error");
			user_numberInfo.text("");
			user_numberInfo.removeClass("message_error2");
			return true;
		}
	}
	/*function validate_user_number()
	{
		var isvalidemailflag = 0;
		if(jQuery("#user_number").val() == '')
		{
			isvalidemailflag = 1;
		}else
		if(jQuery("#user_number").val() != '')
		{
			var a = jQuery("#user_number").val();
			var filter = /^(1\s*[-\/\.]?)?(\((\d{3})\)|(\d{3}))\s*[-\/\.]?\s*(\d{3})\s*[-\/\.]?\s*(\d{4})\s*(([xX]|[eE][xX][tT])\.?\s*(\d+))*$/; 
			//if it's valid email
			if(filter.test(a)){
				isvalidemailflag = 0;
			}else{
				isvalidemailflag = 1;	
			}
		}
		if(isvalidemailflag)
		{
			user_number.addClass("error");
			user_numberInfo.text("Please Enter valid Contact Number");
			user_numberInfo.addClass("message_error2");
			return false;
		}else
		{
			user_number.removeClass("error");
			user_numberInfo.text("");
			user_numberInfo.removeClass("message_error");
			return true;
		}
	} */

	function validate_user_position()
	{
		if(jQuery("#user_position").val() == '')
		{
			user_position.addClass("error");
			user_positionInfo.text("Please Enter Your Position In The Business");
			user_positionInfo.addClass("message_error2");
			return false;
		}
		else{
			user_position.removeClass("error");
			user_positionInfo.text("");
			user_positionInfo.removeClass("message_error2");
			return true;
		}
	}

	
	function validate_user_comments()
	{
		if(jQuery("#user_comments").val() == '')
		{
			user_comments.addClass("error");
			user_commentsInfo.text("Please Enter Comments");
			user_commentsInfo.addClass("message_error2");
			return false;
		}
		else{
			user_comments.removeClass("error");
			user_commentsInfo.text("");
			user_commentsInfo.removeClass("message_error2");
			return true;
		}
	}

});

