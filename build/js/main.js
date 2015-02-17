var app = {
  apiUrl: 'http://52.10.69.156/vampz/php/',
  name: 'Test123',
  ajaxTimeout: 10000,
  ajaxTimeoutText: 'Looks like those pesky Werewolves are playing around with things again!',
  energyGainInterval: 15,
  theme: {
    bodyBackground: '#463333'/*'#fff'*/,
    headerBackground: '#300',
    headerBorderBottom: '#111',
    footerBackground: '#300'/*'#fff'*/
  },
  mainNavLabels: ['Home', 'Missions', 'Battle', 'Abilities', 'Coven', 'More...'],
  mainNavIcons: ['university', 'university', 'group', 'group', 'group', 'university']
};

var player = {
  deviceId: 123456789,
  level: 1,
  exp: 0,
  explvl: 3,
  blood: 0,
  atk: null,
  def: null,
  energy: 10,
  energyMax: 10,
  secondsToMaxEnergy: 0,
  currentArea: 0,
  areasUnlocked: 1,
};

var energyCountdownTick;

function formatNumber(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function applyTheme() {
  $('body').css('background', app.theme.bodyBackground);
  $('#header').css({'background': app.theme.headerBackground, 'border-bottom': app.theme.headerBorderBottom});
  $('#footer').css('background', app.theme.footerBackground);
  $('#app-name').text(app.name);
}

function buildNav() {
  for ( var i = 0; i < app.mainNavIcons.length; i++ ) {
    $('#footer').append('<div class="col-xs-2 text-center nav-item" onclick="nav(' + (i + 1) + ')"><i class="fa fa-' + app.mainNavIcons[i] + '"></i><br/><span class="nav-label">' + app.mainNavLabels[i] + '</span></div>');
  }
}

function displayStats() {      
  // Get user object from the server and update stats
  $.get(app.apiUrl + 'api.php?action=get-stats', { deviceId: player.deviceId }, function (data) {
    updateStats(data.user);
  }, 'json');
}

function updateStats(userObj) {
  
  // update model/DOM if neccessary
  if (userObj.level !== undefined) { $('#stat-level').text(userObj.level); }

  if (userObj.exp !== undefined) { 
    var expPerc = (userObj.exp / userObj.explvl) * 100;
    $('.progress-bar').css('width', expPerc + '%'); 
    $('#exp-values').text(userObj.exp + '/' + userObj.explvl)
  }

  if (userObj.blood !== undefined) { $('#stat-blood').text(formatNumber(userObj.blood)); }
  if (userObj.energy !== undefined) { player.energy = userObj.energy; $('#stat-energy').text(userObj.energy); }
  if (userObj.max_energy !== undefined) { $('#stat-energy-max').text(userObj.max_energy); }
  
  player.areasUnlocked = userObj.areasUnlocked;
 
  // housekeeping - stop the timeout every time we update the stats. Restart it if there are > 0 seconds to max energy
  // AND the player has not levelled up. If they have just levelled up, replenish all energy and do not start a timer.
  clearTimeout(energyCountdownTick);
  if (userObj.secondsToMaxEnergy > 0 && !userObj.levelUp) {

    // initalise client side energy countdown
    startEnergyCountdown(userObj.secondsToMaxEnergy);
  }
  else {
    $('#energy-timer').text('Full energy');
  }
 
}

function loadPage(tpl, script) {

  showLoading();
  $('.dynamic-value').html('<i class="fa fa-spinner fa-spin"></i>');

  var request = $.ajax({
    type: 'GET',
    url: app.apiUrl + script,
    data: { },
    timeout: app.ajaxTimeout,
    dataType: 'json'
  });

  request.done(function(data) {

    window.scrollTo(0,0);
    $('#content').html($('#' + tpl + '-tpl').html());

    if (tpl == 'stats') {
      $('#stat-atk').text(data.user.atk);
      $('#stat-def').text(data.user.def);
      $('#stat-battles-won').text(data.user.battlesWon);
      $('#stat-sp').text(data.user.sp);
      $('#stat-glory').text(data.user.glory);
    }

    if (tpl == 'missions') {

      missionsHTML = '';
      
      var missionId;

      //console.log('area', data.area);
      $('#area-buttons').find('.btn').eq(data.area - 1).addClass('active');

      // loop through all mission data and inject into #missions-table
      $.each(data.missionData, function (i, mission) {

        missionId = ((data.area - 1) * 10 + (i + 1));

        missionsHTML += '<div class="panel">';
        missionsHTML += '<div class="panel-heading" style="position:relative">' + mission[0] + '<div class="mission-mastery-fill-holder"><div id="mission-mastery-fill-' + missionId + '" class="mission-mastery-fill" style="width:' + data.user['m' + missionId] + '%">';

        if (data.user['m' + (i + 1)] == 100) {
          missionsHTML += 'Mastered <i class="fa fa-star yellow-text"></i>';
        }
        else {
          missionsHTML += data.user['m' + missionId] + '%';
        } 
        
        missionsHTML += '</div></div></div>';
        missionsHTML += '<div class="panel-body">';
        missionsHTML += '<table class="table">';
        missionsHTML += '<tr><td class="col-xs-1"><i class="fa fa-tint red-text"></i> <span class="red-text">' + mission[1] + '-' + Math.floor(mission[1] * 1.1) + '</span><br/>Experience +' + missionId + '<br/>';

        // if any abilities are required, display them
        if (undefined != mission[3]) {
          for (var key in mission[3]) {
            missionsHTML += mission[3][key] + ' x Ability ' + key + ', ';
          }
        }

        missionsHTML += '</td>';

        if (data.user.level >= parseInt(mission[2])) {
          missionsHTML += '<td class="col-xs-1"><button class="btn" onclick="doMission(' + missionId + ')"><i class="fa fa-bolt yellow-text"></i> ' + missionId + '</button></td></tr>';
        }
        else {
          missionsHTML += '<td class="col-xs-1">Unlocked at Level ' + mission[2] + '</td></tr>';
        }

        missionsHTML += '</table>';
        missionsHTML += '</div>';
        missionsHTML += '</div>';
       
      });

      $('#missions-table').html(missionsHTML);

    }

    if (tpl == 'battle') {

      playerList = '';
      
      playerList += '<table class="table table-striped table-bordered">';
      $.each(data.players, function (i, player) {
        playerList += '<tr><td class="col-xs-1"><span class="blue-text">Player ' + player.id + '</span><br/>Level ' + player.level + '<br/>Coven Size: 500</td><td class="col-xs-1"><button class="btn" onclick="battle(' + player.id + ')">Battle <i class="fa fa-bolt yellow-text"></i> 1</button></td></tr>';
      });
      playerList += '</table>';

      $('#attackable-players-list').html(playerList);
    }

    if (tpl == 'abilities') {

      abiltiesHTML = '';

      // fetch list of shard offers from backend
      abiltiesHTML += '<table class="table table-striped table-bordered">';
      $.each(data.abilities, function (i, ability) {
        abiltiesHTML += '<tr><td class="col-xs-1">' + ability[0] + '<br/>+' + ability[1] + ' Atk +' + ability[2] + ' Def</td><td class="col-xs-1"><button class="btn" onclick="buyAbility(' + (i + 1) + ')">Buy for ' + formatNumber(ability[3]) + ' <i class="fa fa-tint red-text"></i></button></td></tr>';
      });
      abiltiesHTML += '</table>';

      $('#abilities-list').html(abiltiesHTML);

    }

    if (tpl == 'coven') {
      console.log(data);

      if (data.players.length == 0) {
        $('#coven-members-list').html('<p>You have not recruited anyone to your Coven yet.</p>');
      }
      else {

        coverListHTML = '';

        // fetch list of shard offers from backend
        coverListHTML += '<table class="table table-striped table-bordered">';
        $.each(data.players, function (i, player) {
          coverListHTML += '<tr><td class="col-xs-1">Player #' + player.pid + '</td><td class="col-xs-1">Level ' + player.level + '</td></tr>';
        });
        coverListHTML += '</table>';

        $('#coven-members-list').html(coverListHTML);
      }
    }

    if (tpl == 'shrine') {

      console.log(data);
      shardPacksHTML = '';
      upgradeListHTML = '';

      // fetch list of shard offers from backend
      shardPacksHTML += '<table class="table table-striped table-bordered">';
      $.each(data.packs, function (i, pack) {
        shardPacksHTML += '<tr><td class="col-xs-1">' + pack[0] + ' <i class="fa fa-codepen green-text"></i></td><td class="col-xs-1"><button class="btn" onclick="buyShards(' + (i + 1) + ')">Buy for $' + pack[1] + '</button></td></tr>';
      });
      shardPacksHTML += '</table>';

      upgradeListHTML += '<table class="table table-striped table-bordered">';
      $.each(data.upgrades, function (i, upgrade) {  
        upgradeListHTML += '<tr><td class="col-xs-1">' + upgrade[0] + '</td><td class="col-xs-1"><button class="btn" onclick="buyUpgrade(' + (i + 1) + ')">' + upgrade[1] + ' <i class="fa fa-codepen green-text"></i></button></td></tr>';
      });
      upgradeListHTML += '</table>';

      $('#shard-packs').html(shardPacksHTML);
      $('#upgrades-list').html(upgradeListHTML);
    }

    if (tpl == 'skills') {
      console.log(data);
      $('#stat-sp').text(data.user.sp);
      $('.stat-atk').text(data.user.atk);
      $('.stat-def').text(data.user.def);
      $('.stat-energyMax').text(data.user.max_energy);
    }

    if (tpl == 'trophies') {
      console.log(data);
    }

    if (tpl == 'more') {
      console.log(data);
    }

    hideLoading();
  });

  request.fail(function(jqXHR, textStatus) {
    hideLoading();
    showModal(app.ajaxTimeoutText);
  });
}

$(function () {
  applyTheme();
  buildNav();
  loadPage('stats', 'api.php?action=page-stats');
  displayStats();
});

function help(id) {
  switch (id) {
    case 1: $('#content').append('<div id="popup" class="row"><p>This is a test popup.</p></div>'); break;
  }

  $('#popup').append('<p><button onclick="removePopup()">Close</button></p>');
}

function popup(text) {
  removePopup();
  $('#content').append('<div id="popup" class="row"><p>' + text + '</p></div>');
  $('#popup').append('<p><button onclick="removePopup()">Close</button></p>');
}

function removePopup() {
  $('#popup').remove();
}

function showLoading() {
  $('#loading').show();
  $('.notification').hide();
}

function hideLoading() {
  $('#loading').hide();
}

function nav(id) {

  if (id === 1) { loadPage('stats', 'api.php?action=page-stats'); }
  else if (id === 2) { loadPage('missions', 'missions.php?area=1'); }
  else if (id === 3) { loadPage('battle', 'battle.php?action=get-attackable-players'); }
  else if (id === 4) { loadPage('abilities', 'abilities.php?action=get-abilities'); }
  else if (id === 5) { loadPage('coven', 'coven.php'); }
  else if (id === 6) { loadPage('more', 'api.php?action=get-shrine-data'); }
  else if (id === 7) { loadPage('skills', 'api.php?action=get-skills'); }
  else if (id === 8) { loadPage('shrine', 'api.php?action=get-shrine-data'); }
  else if (id === 9) { loadPage('trophies', 'trophies.php'); }

}

function changeArea(area) {
  loadPage('missions', 'missions.php?area=' + area);
}

function addPlayer() {
  showLoading();
  $('.notification, .alert').remove();

  var request = $.ajax({
    type: 'GET',
    url: app.apiUrl + 'coven.php',
    data: { action: 'add-player', code: $('#player-code').val() },
    timeout: app.ajaxTimeout,
    dataType: 'json'
  });

  request.done(function(data) {
    
    hideLoading();
    console.log(data);

    if (!data.exists) {
      $('#player-code').before('<div class="alert alert-warning">Sorry, a player with that code does not exist.</div>');
    }
    else {
      $('#player-code').before('<div class="alert alert-warning">Player ' + $('#player-code').val() + ' has been added to your Coven!</div>');
    }

    $('#player-code').val('');

  });
  request.fail(function(jqXHR, textStatus) {
    hideLoading();
    showModal(app.ajaxTimeoutText);

    window.scrollTo(0,0);
  });
}

function battle(id) {
  
  showLoading();
  $('.notification, .alert').remove();

  var request = $.ajax({
    type: 'GET',
    url: app.apiUrl + 'battle.php',
    data: { action: 'battle-player', id: id },
    timeout: app.ajaxTimeout,
    dataType: 'json'
  });

  request.done(function(data) {
    
    hideLoading();
    console.log(data);

    if (!data.error) {
      updateStats(data.user);
      //console.log(data.missionid);
      battleFeedback = '';

      battleFeedback += '<div class="alert alert-warning alert-dismissable"><strong>You battled Player ' + data.battle.id + '!</strong><br/>You gained <b>' + data.battle.expgain + ' EXP</b> and <b>' + data.battle.bloodgain + '</b> <i class="fa fa-tint red-text"></i><br/>You used up 1 <i class="fa fa-bolt"></i>';
      
      if (data.user.levelUp) {
        battleFeedback += '<br/><br/>Congratulations, you are now level <b>?</b>!<br/><br/>* You received 3 SP and your Energy has been fully replenished.';
      }

      battleFeedback += '<br/><br/><button class="btn" onclick="battle(1)">Battle Again</button></div>';
      $('#attackable-players').before(battleFeedback);
    }
    else {
      $('#attackable-players').before('<div class="alert alert-warning alert-dismissable">You don\'t have enough Energy to battle.</div>');
    }

    window.scrollTo(0,0);
  });
  request.fail(function(jqXHR, textStatus) {
    hideLoading();
    showModal(app.ajaxTimeoutText);

    window.scrollTo(0,0);
  });
}

function doMission(id) {

  showLoading();
  $('.notification, .alert').remove();

  var request = $.ajax({
    type: 'GET',
    url: app.apiUrl + 'missions.php',
    data: { action: 'do-mission', missionid: id },
    timeout: app.ajaxTimeout,
    dataType: 'json'
  });

  request.done(function(data) {
    
    hideLoading();
    console.log(data);

    if (!data.error) {
      updateStats(data.user);
      //console.log(data.missionid);
      missionFeedback = '';

      missionFeedback += '<div class="alert alert-warning alert-dismissable"><strong>' + data.mission.title + ' - Complete!</strong><br/>You gained <b>' + data.mission.expgain + ' EXP</b> and <b>' + data.mission.bloodgain + '</b> <i class="fa fa-tint red-text"></i><br/>You used up ' + data.mission.energyused + ' <i class="fa fa-bolt"></i>';
      
      if (data.user.levelUp) {
        missionFeedback += '<br/><br/>Congratulations, you are now level <b>' + data.user.level + '</b>!<br/><br/>* You received 3 SP and your Energy has been fully replenished.';
      }
      
      if (data.newmastery == 100) {
        $('#mission-mastery-fill-' + data.mission.id).html('Mastered <i class="fa fa-star yellow-text"></i>');

        if (data.justmastered) {
          missionFeedback += '<br/><br/>*** NICE! MISSION MASTERED (+3 SP) ***';
        }
      }
      else {
        $('#mission-mastery-fill-' + data.mission.id).text(data.newmastery + '%');
      }

      $('#mission-mastery-fill-' + data.mission.id).css('width', data.newmastery + '%');

      missionFeedback += '<br/><br/><button class="btn" onclick="doMission(' + data.mission.id + ')">Repeat</button></div>';
      $('#missions-table').before(missionFeedback);
      
    }
    else {
      $('#missions-table').before('<div class="alert alert-warning alert-dismissable">You don\'t have enough Energy to do that.</div>');
    }

    window.scrollTo(0,0);
  });
  request.fail(function(jqXHR, textStatus) {
    hideLoading();
    showModal(app.ajaxTimeoutText);

    window.scrollTo(0,0);
  });
  
}

function upgradeSkill(id) {

  showLoading();
  $('.alert').remove();

  var request = $.ajax({
    type: 'GET',
    url: app.apiUrl + 'api.php',
    data: { action: 'upgrade-skill', id: id },
    timeout: app.ajaxTimeout,
    dataType: 'json'
  });

  request.done(function(data) {
    
    hideLoading();

    if (!data.upgraded) {
      
      responseHTML = '<div class="alert alert-warning">'
                   + 'Not enough SP.'
                   + '</div>';
    }
    else {
      
      responseHTML = '<div class="alert alert-warning">'
                   + 'You have upgraded your ' + data.skillname + ' by 1!'
                   + '</div>';

      $('#stat-sp').text(data.sp);

      newVal = parseInt($('#content .stat-' + data.elemId).text()) + 1;
      $('.stat-' + data.elemId).text(newVal);

      // kick off the energy timer if they player has upgraded their max energy
      if (data.elemId == 'energyMax') {
        $('#stat-energy-max').text(newVal);
        clearTimeout(energyCountdownTick);
        startEnergyCountdown(data.user.secondsToMaxEnergy);
      }
    }

    $('#skills-table').before(responseHTML);

  });
  request.fail(function(jqXHR, textStatus) {
    hideLoading();
    showModal(app.ajaxTimeoutText);
  });
}

function buyUpgrade(id) {

  showLoading();
  $('.notification, .alert').remove();

  var request = $.ajax({
    type: 'GET',
    url: app.apiUrl + 'api.php',
    data: { action: 'buy-upgrade', id: id },
    timeout: app.ajaxTimeout,
    dataType: 'json'
  });

  request.done(function(data) {
    hideLoading();
    //$('#missions-table').before('<p class="notification">' + data.resp + '</p>');
    console.log(data);
    $('#spend-shards').before('<p class="notification">' + data.upgradename + '</p>');

    // if (!data.error) {
    //   updateStats(data.user);
    //   console.log(data.missionid);
    //   $('#mission-mastery-' + data.missionid).text(data.newmastery);
    //   //console.log(data.user);
    // }
    // else {
    //   $('#missions-table').before('<div class="alert alert-warning alert-dismissable">You don\'t have enough Energy to do that.</div>');
    // }

    window.scrollTo(0,0);
  });
  request.fail(function(jqXHR, textStatus) {
    hideLoading();
    showModal(app.ajaxTimeoutText);

    window.scrollTo(0,0);
  });
  
}

function startEnergyCountdown(seconds) {
  
  seconds--;

  if (seconds % app.energyGainInterval == 0) {
    //increase energy by 1 (client side)
    player.energy++;
    $('#stat-energy').text(player.energy);
  }

  //format timer
  var secsUntilNextIncrease = seconds % app.energyGainInterval; // 22 = 2, 20 = 0

  if (secsUntilNextIncrease == 0) {
    secsUntilNextIncrease = app.energyGainInterval;
  }

  secsUntilNextIncrease > 9 ?
    formattedTime = '0:' + secsUntilNextIncrease :
    formattedTime = '0:0' + secsUntilNextIncrease;

  $('#energy-timer').text(formattedTime);
  
  if (seconds > 0) {
    energyCountdownTick = setTimeout(function () {
      startEnergyCountdown(seconds);
    }, 1000);
  }
  else {
    $('#energy-timer').text('Full energy');
  }

}

/* Utility functions */
function showModal(content) {
  alert('Woops!');
}