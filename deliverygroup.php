<style>
.virtual_table .headRow {
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  -webkit-justify-content: space-between;
  justify-content: space-between;
  background: #e1e1e1;
  padding: 10px;
}

.virtual_table .headRow .divCell {
  font-weight: 700;
}

.virtual_table .divRow {
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  -webkit-justify-content: space-between;
  justify-content: space-between;
  padding: 10px;
  -webkit-box-align: center;
  -moz-box-align: center;
  -ms-flex-align: center;
  -webkit-align-items: center;
  align-items: center;
}

.virtual_table .divRow:nth-child(even) {
  background: #ffffff;
}

.divCell {
  flex-grow: 1;
}

.virtual_table .divRow>.divCell {
  flex-basis: 14%;
}

.virtual_table .divRow .rowdiv {
  flex-grow: 2;
  width: 50%;
}

.virtual_table .divRow .slotRowbinder {
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  padding-bottom: 10px;
  -webkit-box-align: center;
  -moz-box-align: center;
  -ms-flex-align: center;
  -webkit-align-items: center;
  align-items: center;
  -webkit-justify-content: space-between;
  justify-content: space-between;
}

.virtual_table .divRow .slotRowbinder:last-child {
  padding-bottom: 0px;
}

.virtual_table .divRow .slotRowbinder select {
  height: 35px;
}
</style>

<script>
$(document).ready(function() {
  $(document).on("change", "#validation_type", function() {
    console.log($(this).val());
    if ($(this).val() == '3') {
      $("label[for*='validation_code']").html("States");
    } else {
      $("label[for*='validation_code']").html("Zip Codes");
    }
  });

  if ($('#validation_type').val() == '3') {
    $("label[for*='validation_code']").html("States");
  }
});
</script>
<?php 
function getCurrentTimeInMilitaryFormat($today) {
    $hour = $today->format('H');
    $minutes = $today->format('i');
    $scaled_minutes = round(($minutes / 60) * 100);
    return sprintf("%02d%02d", $hour, $scaled_minutes);
}

function getFinalDeliveryDay($current_time, $current_day_index, $offset_data) {
    $days_of_week = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

    // Extract breakpoints and offsets
    $enable_breakpoint = $offset_data['enable_breakpoint'];
    $nextday_breakpoint = $offset_data['nextday_breakpoint'];
    $before_offset = (int) $offset_data['before'];
    $after_offset = (int) $offset_data['after'];

    // Determine the final delivery day
    if ($current_time < $enable_breakpoint) {
        // If before the enable breakpoint, add 'before_offset' days
        $final_day_index = ($current_day_index + $before_offset) % 7;
    } else {
        // If after the enable breakpoint, check the nextday_breakpoint
        if ($current_time < $nextday_breakpoint) {
            // If before the nextday_breakpoint, add 1 day
            $final_day_index = ($current_day_index + 1) % 7;
        } else {
            // If after the nextday_breakpoint, add 'after_offset' days
            $final_day_index = ($current_day_index + $after_offset) % 7;
        }
    }

    // Return the final delivery day name
    return $days_of_week[$final_day_index];
}

function checkBlckoutDate($shopify_datas, $today) {
	$seven_days_later = clone $today;
	$seven_days_later->modify('+7 days');

	$blackout_days = $shopify_datas[$delivery]['blackout'];
	$upcoming_blackouts = [];

	foreach ($blackout_days as $date) {
		$blackout_date = new DateTime($date);
		if ($blackout_date >= $today && $blackout_date <= $seven_days_later) {
			// Get the day name (e.g., "Saturday")
			$day_name = $blackout_date->format('l'); 
			$upcoming_blackouts[] = $day_name;
		}
	}

	return $upcoming_blackouts;
	
}

function checkDisableDate ($days_of_week,$shopify_datas){
	$disabled_days = [];

	foreach ($days_of_week as $day) {
		if (!empty($shopify_datas[$delivery]['slot'][$day . '_disable'])) {
			$disabled_days[] = ucfirst($day); // Convert "sunday" to "Sunday"
		}
	}
	return $disabled_days;

}

function nearestDate($date, $shopify_datas, $days_of_week) {
    $temp_date = clone $date;
    $attempts = 0;
	$disabled_days = checkDisableDate($days_of_week, $shopify_datas);

    while ($attempts < 30) { 
        $today_day_name = $temp_date->format('l');

        $upcoming_blackouts = checkBlckoutDate($shopify_datas, $temp_date);

        if (!in_array($today_day_name, $upcoming_blackouts) && !in_array($today_day_name, $disabled_days)) {
            return $temp_date;
        }

        // Move to the next day
        $temp_date->modify('+1 day');
        $attempts++;
    }

    return null;
}


$timing=array("600"=>'6:00AM',"650"=>'6:30AM',"700"=>'7:00AM',"750"=>'7:30AM',"800"=>'8:00AM', "850"=>'8:30AM', "900"=>'9:00AM', "950"=>'9:30AM', "1000"=>'10:00AM', "1050"=>'10:30AM', "1100"=>'11:00AM', "1150"=>'11:30AM', "1200"=>'12:00PM', "1250"=>'12:30PM', "1300"=>'1:00PM', "1350"=>'1:30PM', "1400"=>'2:00PM', "1425"=>'2:15PM' ,"1450"=>'2:30PM', "1475"=>'2:45PM',"1500"=>'3:00PM', "1525"=>'3:15PM', "1550"=>'3:30PM', "1575"=>'3:45PM', "1600"=>'4:00PM', "1650"=>'4:30PM', "1700"=>'5:00PM', "1750"=>'5:30PM', "1800"=>'6:00PM', "1850"=>'6:30PM',"1900"=>'7:00PM', "1930"=>'7:30PM',"2000"=>'8:00PM',"2050"=>'8:30PM',"2100"=>'9:00PM',"2150"=>'9:30PM',"2200"=>'10:00PM',"2250"=>'10:30PM',"2300"=>'11:00PM',"2350"=>'11:30PM',"2400"=>'12:00AM');
$shopify_datas=json_decode($deliver, true); 
?>
<script>
console.log('shopify data', <?php echo json_encode($shopify_datas); ?>);
</script>
<?php
// $method = trim($method);
$method_datas=json_decode($method, true); 
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON Decode Error: " . json_last_error_msg();
}
$delivery='delivery_'.$detail;

date_default_timezone_set('Australia/Sydney');

$days_of_week = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
$today = new DateTime();
$seven_days_later = new DateTime();
$today_day_name = $today->format('l');
$current_day_index = $today->format('w');
$todayLower = strtolower($today_day_name);
$iterations = 0;
$current_time = getCurrentTimeInMilitaryFormat($today);

$enable_breakpoint = $shopify_datas[$delivery]['offset'][$todayLower]['enable_breakpoint'] ?? null;
$nextday_breakpoint = $shopify_datas[$delivery]['offset'][$todayLower]['nextday_breakpoint'] ?? null;
$before_offset = isset($shopify_datas[$delivery]['offset'][$todayLower]['before']) ? (int) $shopify_datas[$delivery]['offset'][$todayLower]['before'] : 0;
$after_offset = isset($shopify_datas[$delivery]['offset'][$todayLower]['after']) ? (int) $shopify_datas[$delivery]['offset'][$todayLower]['after'] : 0;

$upcoming_blackouts = checkBlckoutDate($shopify_datas, $today, $seven_days_later);
$disabled_days = checkDisableDate($days_of_week, $shopify_datas);
$availableDate = nearestDate($today, $shopify_datas, $days_of_week);
 echo $availableDate->format('Y-m-d');
// echo $upcoming_blackouts;


// $final_day_index = null;  

// if ($availableDateObj->format('Y-m-d') == $today->format('Y-m-d')) {
//     if ($current_time < $enable_breakpoint) {
//         $final_day_index = ($current_day_index + $before_offset) % 7;
//     } else {
//         if ($current_time < $nextday_breakpoint) {
//             $final_day_index = ($current_day_index + 1) % 7;
//         } else {
//             $final_day_index = ($current_day_index + $after_offset) % 7;
//         }
//     }
// }

// if ($final_day_index !== null) {
//     if ($final_day_index == $current_day_index) {
//         $messageDisplay = "Today's date is still available for purchase till " . ($timing[$enable_breakpoint] ?? "Unknown Time") . ".";
//     } else {
//         $messageDisplay = "Today's date is not available for purchase. Nearest available date is coming on " . $days_of_week[$final_day_index] . ".";
//     }
// } else {
//     $messageDisplay = "Today's date is not available for purchase. Nearest available date is " . $availableDate . ".";
// }

// echo $messageDisplay;


?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Locations</h1>
  </section>
  <section class="content">
    <h2>Delivery Information</h2>
    <?php echo $method_datas; ?>
    <?php if($delivery_basic_success){ ?>
    <div class="success_basic">
      <h3 style="color:green">Location Detail Successfully Saved.</h3>
    </div>
    <?php } ?>
    <div class="location_basic">
      <form method="POST" action="<?php echo base_url('delivery/deliverdetail').'/'.$detail; ?>" name="basic_info"
        id="basic_info">
        <table>
          <tr>
            <td><label for="enable_delivery">Enable Delivery</label></td>
            <td><input type="checkbox" name="enable_delivery" id="enable_delivery" value="1"
                <?php if($shopify_datas[$delivery]['enable_delivery']){ echo 'checked'; }?> /></td>
          </tr>
          <tr>
            <td><label for="delivery_tag">Delivery Tag Name</label></td>
            <td><input type="text" name="delivery_tag" id="delivery_tag"
                value="<?php echo $shopify_datas[$delivery]['delivery_tag']; ?>" required /></td>
          </tr>
          <tr>
            <td><label for="delivery_tag">Delivery Description</label></td>
            <td><textarea rows="8" name="delivery_description" id="delivery_description"
                style="width:100%"><?php echo $shopify_datas[$delivery]['delivery_description']; ?></textarea></td>
          </tr>
          <input type="hidden" value="<?php echo $detail; ?>" name="delivery_groupid" />
          <tr>
            <td></td>
            <td><input type="submit" name="basic_delivery" id="basic_delivery" value="Save"></td>
          </tr>
        </table>
      </form>
    </div>
  </section>

  <section class="content">
    <h2>Validation Zip Code</h2>
    <?php if($delivery_zip_success){ ?>
    <div class="success_delivery_zipcode">
      <h3 style="color:green">Zip Code Saved successfully.</h3>
    </div>
    <?php } ?>

    <div class="zipcode_validate">
      <form method="POST" action="<?php echo base_url('delivery/deliverdetail').'/'.$detail; ?>" name="basic_info"
        id="basic_info">
        <table class="table-left-align">
          <tr>
            <td><label for="enable_zipcode_validate">Enable Zip Code Validation</label></td>
            <td><input type="checkbox" name="enable_zipcode_validate" id="enable_zipcode_validate" value="1"
                <?php if($shopify_datas[$delivery]['zipcode']['enable_zipcode']){ echo 'checked'; } ?> /></td>
          </tr>
          <tr>
            <td><label for="enable_sameday_delivery">Enable Sameday Delivery </label></td>
            <td><input type="checkbox" name="enable_sameday_delivery" id="enable_sameday_delivery" value="1"
                <?php if($shopify_datas[$delivery]['zipcode']['enable_sameday_delivery']){ echo 'checked'; } ?> /></td>
          </tr>
          <tr>
            <td><label for="validation_type">Zip Code Validation Type</label></td>
            <td>
              <select name="validation_type" id="validation_type">
                <option value="">Select Options</option>
                <option value="1"
                  <?php if($shopify_datas[$delivery]['zipcode']['validation_type']==1){ echo 'selected'; } ?>>Exact
                  Match</option>
                <option value="2"
                  <?php if($shopify_datas[$delivery]['zipcode']['validation_type']==2){ echo 'selected'; } ?>>Prefix
                  match</option>
                <option value="3"
                  <?php if($shopify_datas[$delivery]['zipcode']['validation_type']==3){ echo 'selected'; } ?>>State
                  Match</option>
              </select>
          <tr>
            <td><label for="validation_code">Zip Codes</label></td>
            <td><textarea rows="8" name="validation_code" id="validation_code"
                style="width:100%"><?php echo trim($shopify_datas[$delivery]['zipcode']['zip_codes']);  ?></textarea>
            </td>
          </tr>
          <tr>
            <style>
							.table-left-align td{
								text-align: left;
							}


            td.vertical-top {
              vertical-align: top;
							width: 40%;
            }
						td.vertical-top label{
							margin-top: 5px;
						}

            #searchZip {
              padding: 0 6px;
              margin-bottom: 10px;
            }
						#searchZip:focus{
							outline:none;
						}
            .zipcode-group {
              display: none;
              margin-bottom: 10px;
              padding: 6px;
              border: 1px solid #000;
							background-color:#fff;
            }
						ul.search-result-list {
							padding-left: 20px;
							font-size:12px;
							line-height:1.1;
						}
						#notFound { color: red; display: none; }
            </style>
            <?php
							// Load JSON file
							$jsonData = file_get_contents("postcodes_suburbs.json");
							$data = json_decode($jsonData, true);

							// Group suburbs by postcode
							$postcodeGroups = [];
							foreach ($data as $item) {
									$postcode = $item['postcode'];
									$suburb   = $item['suburb'];
									$postcodeGroups[$postcode][] = $suburb;
							}
							?>
            <td class="vertical-top"><label>Search Suburbs by Zip Code</label></td>
            <td>

              <input type="text" id="searchZip" placeholder="Enter Zip Code">
							 <p id="notFound">❌ No suburbs found for that Zip Code.</p>
              <div id="zipGroups">
                <?php foreach ($postcodeGroups as $postcode => $suburbs): ?>
                <div class="zipcode-group" id="zip-<?php echo $postcode; ?>">
                  <p>Search results for Zip Code: <strong><?php echo $postcode; ?></strong></p>
                  <ul class="search-result-list">
                    <?php foreach ($suburbs as $suburb): ?>
                    <li><?php echo htmlspecialchars($suburb); ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
                <?php endforeach; ?>
              </div>

               <script>
									 const input = document.getElementById("searchZip");
									const notFound = document.getElementById("notFound");

									input.addEventListener("keyup", function() {
											let value = input.value.trim();
											let found = false;

											// Hide all groups first
											document.querySelectorAll(".zipcode-group").forEach(div => {
													div.style.display = "none";
											});
											notFound.style.display = "none"; // hide "not found" by default

											if (value.length === 4) { // only check when 4 digits entered
													let target = document.getElementById("zip-" + value);
													if (target) {
															target.style.display = "block";
															found = true;
													}

													if (!found) {
															notFound.style.display = "block";
													}
											}
									});
							</script>
            </td>
          </tr>
          <tr>
            <td><label for="enable_suburb_restriction">Enable Suburb Restrictions </label></td>
            <td><input type="checkbox" name="enable_suburb_restriction" id="enable_suburb_restriction" value="1"
                <?php if($shopify_datas[$delivery]['zipcode']['enable_suburb_restriction']){ echo 'checked'; } ?> />
            </td>
          </tr>
          <tr>
            <td><label for="validation_suburb">Restricted Suburbs</label></td>
            <td><textarea rows="12" name="validation_suburb" id="validation_suburb"
                style="width:100%"><?php echo trim($shopify_datas[$delivery]['zipcode']['restricted_suburb']);  ?></textarea>
            </td>
          </tr>
          <tr>
            <td><label for="enable_suburb_available">Enable Suburb Availability </label></td>
            <td><input type="checkbox" name="enable_suburb_available" id="enable_suburb_available" value="1"
                <?php if($shopify_datas[$delivery]['zipcode']['enable_suburb_available']){ echo 'checked'; } ?> /></td>
          </tr>
          <tr>
            <td><label for="available_suburb">Available Suburbs</label></td>
            <td><textarea rows="12" name="available_suburb" id="available_suburb"
                style="width:100%"><?php echo trim($shopify_datas[$delivery]['zipcode']['available_suburb']);  ?></textarea>
            </td>
          </tr>
          <input type="hidden" value="<?php echo $detail; ?>" name="delivery_groupid" />
          <tr>
            <td></td>
            <td><input type="submit" name="delivery_zipcode" id="delivery_zipcode" value="Save"></td>
          </tr>
        </table>
      </form>
    </div>
  </section>

  <section class="content">
    <h2>Delivery Times</h2>
    <?php if($delivery_slot_success){ ?>
    <div class="delivery_slot_success">
      <h3 style="color:green">Delivery Slot Successfully Saved.</h3>
    </div>
    <?php } ?>
    <div class="pickup_times">
      <form method="POST" action="<?php echo base_url('delivery/deliverdetail').'/'.$detail; ?>" name="pickup_time">
        <div class="virtual_table">
          <table>
            <tr>
              <td style="width:20%">Enable Delivery Time</td>
              <td><input type="checkbox" name="enable_deliverytime" id="enable_deliverytime" value="1"
                  <?php if($shopify_datas[$delivery]['slot']['enable_deliverytime']) { echo 'checked'; } ; ?> /></td>
            </tr>
          </table>

          <div class="headRow">
            <div class="divCell">Days</div>
            <div class="divCell">From</div>
            <div class="divCell">Until</div>
            <div class="divCell">Remove</div>
            <div class="divCell">Disable</div>
          </div>
          <div class="divRow">
            <div class="divCell">Sunday</div>
            <div class="rowdiv">
              <?php if($shopify_datas[$delivery]['slot']['sunday']){
						foreach($shopify_datas[$delivery]['slot']['sunday'] as $single_slot){ ?>
              <div class='slotRowbinder'>
                <div class='divCell'>
                  <select name="sunday[start][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['start']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'>
                  <select name="sunday[end][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['end']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'><a href='javascript:void(0);' class='removeCell'>Remove</a></div>
              </div>
              <?php }
					} ?>
            </div>
            <div class='divCell'><input type="checkbox" name="sunday_disable" value="1"
                <?php if($shopify_datas[$delivery]['slot']['sunday_disable']){ echo 'checked'; } ?> /></div>
            <a href="javascript:void(0)" class="slot" data-id="sunday">Add Slot</a>
          </div>

          <div class="divRow">
            <div class="divCell">Monday</div>
            <div class="rowdiv">
              <?php if($shopify_datas[$delivery]['slot']['monday']){ 
						foreach($shopify_datas[$delivery]['slot']['monday'] as $single_slot){ ?>
              <div class='slotRowbinder'>
                <div class='divCell'>
                  <select name="monday[start][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['start']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'>
                  <select name="monday[end][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['end']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'><a href='javascript:void(0);' class='removeCell'>Remove</a></div>
              </div>
              <?php }
					} ?>
            </div>
            <div class='divCell'><input type="checkbox" name="monday_disable" value="1"
                <?php if($shopify_datas[$delivery]['slot']['monday_disable']){ echo 'checked'; } ?> /></div>
            <a href="javascript:void(0)" style="float:right" class="slot" data-id="monday">Add Slot</a>
          </div>

          <div class="divRow">
            <div class="divCell">Tuesday</div>
            <div class="rowdiv">
              <?php if($shopify_datas[$delivery]['slot']['tuesday']){ 
						foreach($shopify_datas[$delivery]['slot']['tuesday'] as $single_slot){ ?>
              <div class='slotRowbinder'>
                <div class='divCell'>
                  <select name="tuesday[start][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['start']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'>
                  <select name="tuesday[end][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['end']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'><a href='javascript:void(0);' class='removeCell'>Remove</a></div>
              </div>
              <?php }
					} ?>
            </div>
            <div class='divCell'><input type="checkbox" name="tuesday_disable" value="1"
                <?php if($shopify_datas[$delivery]['slot']['tuesday_disable']){ echo 'checked'; } ?> /></div>
            <a href="javascript:void(0)" style="float:right" class="slot" data-id="tuesday">Add Slot</a>
          </div>

          <div class="divRow">
            <div class="divCell">Wednesday</div>
            <div class="rowdiv">
              <?php if($shopify_datas[$delivery]['slot']['wednesday']){ 
						foreach($shopify_datas[$delivery]['slot']['wednesday'] as $single_slot){ ?>
              <div class='slotRowbinder'>
                <div class='divCell'>
                  <select name="wednesday[start][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['start']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'>
                  <select name="wednesday[end][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['end']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'><a href='javascript:void(0);' class='removeCell'>Remove</a></div>
              </div>
              <?php }
					} ?>
            </div>
            <div class='divCell'><input type="checkbox" name="wednesday_disable" value="1"
                <?php if($shopify_datas[$delivery]['slot']['wednesday_disable']){ echo 'checked'; } ?> /></div>
            <a href="javascript:void(0)" style="float:right" class="slot" data-id="wednesday">Add Slot</a>
          </div>

          <div class="divRow">
            <div class="divCell">Thursday</div>
            <div class="rowdiv">
              <?php if($shopify_datas[$delivery]['slot']['thursday']){ 
						foreach($shopify_datas[$delivery]['slot']['thursday'] as $single_slot){ ?>
              <div class='slotRowbinder'>
                <div class='divCell'>
                  <select name="thursday[start][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['start']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'>
                  <select name="thursday[end][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['end']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'><a href='javascript:void(0);' class='removeCell'>Remove</a></div>
              </div>
              <?php }
					} ?>
            </div>
            <div class='divCell'><input type="checkbox" name="thursday_disable" value="1"
                <?php if($shopify_datas[$delivery]['slot']['thursday_disable']){ echo 'checked'; } ?> /></div>
            <a href="javascript:void(0)" style="float:right" class="slot" data-id="thursday">Add Slot</a>
          </div>

          <div class="divRow">
            <div class="divCell">Friday</div>
            <div class="rowdiv">
              <?php if($shopify_datas[$delivery]['slot']['friday']){ 
						foreach($shopify_datas[$delivery]['slot']['friday'] as $single_slot){ ?>
              <div class='slotRowbinder'>
                <div class='divCell'>
                  <select name="friday[start][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['start']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'>
                  <select name="friday[end][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['end']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'><a href='javascript:void(0);' class='removeCell'>Remove</a></div>
              </div>
              <?php }
					} ?>
            </div>
            <div class='divCell'><input type="checkbox" name="friday_disable" value="1"
                <?php if($shopify_datas[$delivery]['slot']['friday_disable']){ echo 'checked'; } ?> /></div>
            <a href="javascript:void(0)" style="float:right" class="slot" data-id="friday">Add Slot</a>
          </div>

          <div class="divRow">
            <div class="divCell">Saturday</div>
            <div class="rowdiv">
              <?php if($shopify_datas[$delivery]['slot']['saturday']){ 
						foreach($shopify_datas[$delivery]['slot']['saturday'] as $single_slot){ ?>
              <div class='slotRowbinder'>
                <div class='divCell'>
                  <select name="saturday[start][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['start']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'>
                  <select name="saturday[end][]">
                    <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $single_slot['end']){$selected_echo='selected';} else{ $selected_echo='';}
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
                  </select>
                </div>
                <div class='divCell'><a href='javascript:void(0);' class='removeCell'>Remove</a></div>
              </div>
              <?php }
					} ?>
            </div>
            <div class='divCell'><input type="checkbox" name="saturday_disable" value="1"
                <?php if($shopify_datas[$delivery]['slot']['saturday_disable']){ echo 'checked'; } ?> /></div>
            <a href="javascript:void(0)" style="float:right" class="slot" data-id="saturday">Add Slot</a>
          </div>
          <input type="hidden" value="<?php echo $detail; ?>" name="delivery_groupid" />
        </div>
        <input type="submit" name="delivery_slot" id="delivery_slot" value="Save">
      </form>
    </div>


  </section>

  <section class="content">
    <h2>Offsets and cutoffs</h2>
    <p style="color:green;"><?php echo $messageDisplay; ?></p>
    <?php if($successdiv_delivery_offset){ ?>
    <div class="successdiv_delivery_offset">
      <h3 style="color:green">Offset Successfully Saved.</h3>
    </div>
    <?php } ?>
    <div class="offsets_breakpoint">
      <form method="POST" action="<?php echo base_url('delivery/deliverdetail').'/'.$detail; ?>" name="offset">
        <table>
          <tr>
            <th>Days</th>
            <th>Before Offset</th>
            <th>Break Point ( Sydney time )</th>
            <th>NextDay Cutoff ( Sydney time )</th>
            <th>After Offset</th>
            <th>Status</th>
          </tr>

          <tr>

            <td>Monday</td>

            <td>

              <label for="before_monday_offset">Before Cutoff</label><br />
              <select name="before_monday_offset" id="before_monday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['before'] == "0" ? 'selected' : ''; ?>>Same
                  Day</option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['before'] == "1" ? 'selected' : ''; ?>>
                  Tuesday</option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['before'] == "2" ? 'selected' : ''; ?>>
                  Wednesday</option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['before'] == "3" ? 'selected' : ''; ?>>
                  Thursday</option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['before'] == "4" ? 'selected' : ''; ?>>Friday
                </option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['before'] == "5" ? 'selected' : ''; ?>>
                  Saturday</option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['before'] == "6" ? 'selected' : ''; ?>>Sunday
                </option>
              </select>
            </td>

            <td>
              <span>Use Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['monday']['enable_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_monday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="monday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['monday']['breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <span>Use Nextday Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['monday']['enable_nextday_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_monday_nextday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="monday_nextday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['monday']['nextday_breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <label for="after_monday_offset">After Cutoff</label><br />
              <select name="after_monday_offset" id="after_monday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['after'] == "0" ? 'selected' : ''; ?>>Same
                  Day</option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['after'] == "1" ? 'selected' : ''; ?>>Tuesday
                </option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['after'] == "2" ? 'selected' : ''; ?>>
                  Wednesday</option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['after'] == "3" ? 'selected' : ''; ?>>
                  Thursday</option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['after'] == "4" ? 'selected' : ''; ?>>Friday
                </option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['after'] == "5" ? 'selected' : ''; ?>>
                  Saturday</option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['monday']['after'] == "6" ? 'selected' : ''; ?>>Sunday
                </option>
              </select>
            </td>
            <td>
              <?php if ($today_day_name == 'Monday') { ?>
              <p style="color:green;">Active Cutoffs</p>
              <?php } ?>
              <?php if (in_array('Monday', $upcoming_blackouts)) { ?>
              <p style="color:red;">⚠️ Blackout on Monday!</p>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td>Tuesday</td>
            <td>

              <label for="before_tuesday_offset">Before Cutoff</label><br />
              <select name="before_tuesday_offset" id="before_tuesday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['before'] == '0' ? 'selected' : ''; ?>>Same
                  Day</option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['before'] == '1' ? 'selected' : ''; ?>>
                  Wednesday</option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['before'] == '2' ? 'selected' : ''; ?>>
                  Thursday</option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['before'] == '3' ? 'selected' : ''; ?>>
                  Friday</option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['before'] == '4' ? 'selected' : ''; ?>>
                  Saturday</option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['before'] == '5' ? 'selected' : ''; ?>>
                  Sunday</option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['before'] == '6' ? 'selected' : ''; ?>>
                  Monday</option>
              </select>
            </td>
            <td>
              <span>Use Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['tuesday']['enable_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_tuesday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="tuesday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['tuesday']['breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <span>Use Nextday Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['tuesday']['enable_nextday_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_tuesday_nextday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="tuesday_nextday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['tuesday']['nextday_breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <label for="after_tuesday_offset">After Cutoff</label><br />
              <select name="after_tuesday_offset" id="after_tuesday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['after'] == '0' ? 'selected' : ''; ?>>Same
                  Day</option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['after'] == '1' ? 'selected' : ''; ?>>
                  Wednesday</option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['after'] == '2' ? 'selected' : ''; ?>>
                  Thursday</option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['after'] == '3' ? 'selected' : ''; ?>>Friday
                </option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['after'] == '4' ? 'selected' : ''; ?>>
                  Saturday</option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['after'] == '5' ? 'selected' : ''; ?>>Sunday
                </option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['tuesday']['after'] == '6' ? 'selected' : ''; ?>>Monday
                </option>
              </select>
            </td>
            <td>
              <p style="color:red;"> <?php echo $current_time; ?></p>
              <?php if (in_array('Tuesday', $upcoming_blackouts)) { ?>
              <p style="color:red;">⚠️ Blackout on Tuesday!</p>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td>Wednesday</td>
            <td>

              <label for="before_wednesday_offset">Before Cutoff</label><br />
              <select name="before_wednesday_offset" id="before_wednesday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['before'] == '0' ? 'selected' : ''; ?>>
                  Same Day</option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['before'] == '1' ? 'selected' : ''; ?>>
                  Thursday</option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['before'] == '2' ? 'selected' : ''; ?>>
                  Friday</option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['before'] == '3' ? 'selected' : ''; ?>>
                  Saturday</option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['before'] == '4' ? 'selected' : ''; ?>>
                  Sunday</option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['before'] == '5' ? 'selected' : ''; ?>>
                  Monday</option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['before'] == '6' ? 'selected' : ''; ?>>
                  Tuesday</option>
              </select>
            </td>
            <td>
              <span>Use Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['wednesday']['enable_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_wednesday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="wednesday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['wednesday']['breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <span>Use Nextday Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['wednesday']['enable_nextday_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_wednesday_nextday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="wednesday_nextday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['wednesday']['nextday_breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <label for="after_wednesday_offset">After Cutoff</label><br />
              <select name="after_wednesday_offset" id="after_wednesday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['after'] == '0' ? 'selected' : ''; ?>>Same
                  Day</option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['after'] == '1' ? 'selected' : ''; ?>>
                  Thursday</option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['after'] == '2' ? 'selected' : ''; ?>>
                  Friday</option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['after'] == '3' ? 'selected' : ''; ?>>
                  Saturday</option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['after'] == '4' ? 'selected' : ''; ?>>
                  Sunday</option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['after'] == '5' ? 'selected' : ''; ?>>
                  Monday</option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['wednesday']['after'] == '6' ? 'selected' : ''; ?>>
                  Tuesday</option>
              </select>
            </td>
            <td>
              <?php if (in_array('Wednesday', $upcoming_blackouts)) { ?>
              <p style="color:red;">⚠️ Blackout on Wednesday!</p>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td>Thursday</td>
            <td>

              <label for="before_thursday_offset">Before Cutoff</label><br />
              <select name="before_thursday_offset" id="before_thursday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['before'] == '0' ? 'selected' : ''; ?>>Same
                  Day</option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['before'] == '1' ? 'selected' : ''; ?>>
                  Friday</option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['before'] == '2' ? 'selected' : ''; ?>>
                  Saturday</option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['before'] == '3' ? 'selected' : ''; ?>>
                  Sunday</option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['before'] == '4' ? 'selected' : ''; ?>>
                  Monday</option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['before'] == '5' ? 'selected' : ''; ?>>
                  Tuesday</option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['before'] == '6' ? 'selected' : ''; ?>>
                  Wednesday</option>
              </select>
            </td>
            <td>
              <span>Use Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['thursday']['enable_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_thursday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="thursday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['thursday']['breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <span>Use Nextday Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['thursday']['enable_nextday_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_thursday_nextday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="thursday_nextday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['thursday']['nextday_breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <label for="after_thursday_offset">After Cutoff</label><br />
              <select name="after_thursday_offset" id="after_thursday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['after'] == '0' ? 'selected' : ''; ?>>Same
                  Day</option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['after'] == '1' ? 'selected' : ''; ?>>
                  Friday</option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['after'] == '2' ? 'selected' : ''; ?>>
                  Saturday</option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['after'] == '3' ? 'selected' : ''; ?>>
                  Sunday</option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['after'] == '4' ? 'selected' : ''; ?>>
                  Monday</option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['after'] == '5' ? 'selected' : ''; ?>>
                  Tuesday</option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['thursday']['after'] == '6' ? 'selected' : ''; ?>>
                  Wednesday</option>
              </select>
            </td>
            <td>
              <?php if ($today_day_name == 'Thursday') { ?>
              <p style="color:green;">Active Cutoffs</p>
              <?php } ?>
              <?php if (in_array('Thursday', $upcoming_blackouts)) { ?>
              <p style="color:red;">⚠️ Blackout on Thursday!</p>
              <?php } ?>

            </td>
          </tr>

          <tr>
            <td>Friday</td>
            <td>

              <label for="before_friday_offset">Before Cutoff</label><br />
              <select name="before_friday_offset" id="before_friday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['before'] == '0' ? 'selected' : ''; ?>>Same
                  Day</option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['before'] == '1' ? 'selected' : ''; ?>>
                  Saturday</option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['before'] == '2' ? 'selected' : ''; ?>>Sunday
                </option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['before'] == '3' ? 'selected' : ''; ?>>Monday
                </option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['before'] == '4' ? 'selected' : ''; ?>>
                  Tuesday</option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['before'] == '5' ? 'selected' : ''; ?>>
                  Wednesday</option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['before'] == '6' ? 'selected' : ''; ?>>
                  Thursday</option>
              </select>
            </td>
            <td>
              <span>Use Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['friday']['enable_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_friday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="friday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['friday']['breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <span>Use Nextday Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['friday']['enable_nextday_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_friday_nextday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="friday_nextday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['friday']['nextday_breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <label for="after_friday_offset">After Cutoff</label><br />
              <select name="after_friday_offset" id="after_friday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['after'] == '0' ? 'selected' : ''; ?>>Same
                  Day</option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['after'] == '1' ? 'selected' : ''; ?>>
                  Saturday</option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['after'] == '2' ? 'selected' : ''; ?>>Sunday
                </option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['after'] == '3' ? 'selected' : ''; ?>>Monday
                </option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['after'] == '4' ? 'selected' : ''; ?>>Tuesday
                </option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['after'] == '5' ? 'selected' : ''; ?>>
                  Wednesday</option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['friday']['after'] == '6' ? 'selected' : ''; ?>>
                  Thursday</option>
              </select>
            </td>
            <td>
              <?php if (in_array('Friday', $upcoming_blackouts)) { ?>
              <p style="color:red;">⚠️ Blackout on Friday!</p>
              <?php } ?>
            </td>
          </tr>

          <tr>

            <td>Saturday</td>

            <td>
              <label for="before_saturday_offset">Before Cutoff</label><br />
              <select name="before_saturday_offset" id="before_saturday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['before'] == '0' ? 'selected' : ''; ?>>Same
                  Day</option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['before'] == '1' ? 'selected' : ''; ?>>
                  Sunday</option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['before'] == '2' ? 'selected' : ''; ?>>
                  Monday</option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['before'] == '3' ? 'selected' : ''; ?>>
                  Tuesday</option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['before'] == '4' ? 'selected' : ''; ?>>
                  Wednesday</option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['before'] == '5' ? 'selected' : ''; ?>>
                  Thursday</option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['before'] == '6' ? 'selected' : ''; ?>>
                  Friday</option>
              </select>
            </td>
            <td>
              <span>Use Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['saturday']['enable_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_saturday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="saturday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['saturday']['breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <span>Use Nextday Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['saturday']['enable_nextday_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_saturday_nextday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="saturday_nextday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['saturday']['nextday_breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <label for="after_saturday_offset">After Cutoff</label><br />
              <select name="after_saturday_offset" id="after_saturday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['after'] == '0' ? 'selected' : ''; ?>>Same
                  Day</option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['after'] == '1' ? 'selected' : ''; ?>>
                  Sunday</option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['after'] == '2' ? 'selected' : ''; ?>>
                  Monday</option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['after'] == '3' ? 'selected' : ''; ?>>
                  Tuesday</option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['after'] == '4' ? 'selected' : ''; ?>>
                  Wednesday</option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['after'] == '5' ? 'selected' : ''; ?>>
                  Thursday</option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['saturday']['after'] == '6' ? 'selected' : ''; ?>>
                  Friday</option>
              </select>
            </td>
            <td>
              <?php if (in_array('Saturday', $upcoming_blackouts)) { ?>
              <p style="color:red;">Blacked out comming Saturday</p>
              <?php } ?>
            </td>
          </tr>

          <tr>
            <td>Sunday</td>
            <td>
              <label for="before_sunday_offset">Before Cutoff</label><br />
              <select name="before_sunday_offset" id="before_sunday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['before'] == 0 ? 'selected' : ''; ?>>Same Day
                </option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['before'] == 1 ? 'selected' : ''; ?>>Monday
                </option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['before'] == 2 ? 'selected' : ''; ?>>Tuesday
                </option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['before'] == 3 ? 'selected' : ''; ?>>
                  Wednesday</option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['before'] == 4 ? 'selected' : ''; ?>>Thursday
                </option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['before'] == 5 ? 'selected' : ''; ?>>Friday
                </option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['before'] == 6 ? 'selected' : ''; ?>>Saturday
                </option>
              </select>

            </td>
            <td>
              <span>Use Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['sunday']['enable_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_sunday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="sunday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['sunday']['breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <span>Use Nextday Cutoff?</span><br />
              <?php if($shopify_datas[$delivery]['offset']['sunday']['enable_nextday_breakpoint']==1) { $check_echo='checked'; } else { $check_echo='';}?>
              <input type="checkbox" name="enable_sunday_nextday_breakpoint" value="1" <?php echo $check_echo; ?> />
              <select name="sunday_nextday_breakpoint">
                <?php if($timing){
										foreach($timing as $key=>$time){
											if($key== $shopify_datas[$delivery]['offset']['sunday']['nextday_breakpoint']){ $selected_echo='selected'; } else{ $selected_echo=''; }
											echo '<option value="'.$key.'" '.$selected_echo.'>'.$time.'</option>';
										}
									} ?>
              </select>
            </td>
            <td>
              <label for="after_sunday_offset">After Cutoff</label><br />
              <select name="after_sunday_offset" id="after_sunday_offset">
                <option value="0"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['after'] == 0 ? 'selected' : ''; ?>>Same Day
                </option>
                <option value="1"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['after'] == 1 ? 'selected' : ''; ?>>Monday
                </option>
                <option value="2"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['after'] == 2 ? 'selected' : ''; ?>>Tuesday
                </option>
                <option value="3"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['after'] == 3 ? 'selected' : ''; ?>>Wednesday
                </option>
                <option value="4"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['after'] == 4 ? 'selected' : ''; ?>>Thursday
                </option>
                <option value="5"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['after'] == 5 ? 'selected' : ''; ?>>Friday
                </option>
                <option value="6"
                  <?php echo $shopify_datas[$delivery]['offset']['sunday']['after'] == 6 ? 'selected' : ''; ?>>Saturday
                </option>
              </select>
            </td>
            <td>
              <?php if (in_array('Sunday', $upcoming_blackouts)) { ?>
              <p style="color:red;">⚠️ Blackout on Sunday!</p>
              <?php } ?>
              <?php if (in_array('Sunday', $disabled_days)) { ?>
              <p style="color:gray;">🚫 Sunday is disabled.</p>
              <?php } ?>
            </td>
          </tr>
          <input type="hidden" value="<?php echo $detail; ?>" name="delivery_groupid" />
          <tr>
            <td></td>
            <td><input type="submit" name="delivery_offset" id="delivery_offset" value="Save"></td>
          </tr>
        </table>
      </form>
    </div>
  </section>

  <section class="content">
    <h2>Blackout Dates</h2>
    <?php if($successdiv_blackout){ ?>
    <div class="success_blackout">
      <h3 style="color:green">Blackout Date Successfully Saved.</h3>
    </div>
    <?php } ?>
    <div class="blackout_dates">
      <form method="POST" action="<?php echo base_url('delivery/deliverdetail').'/'.$detail; ?>" name="blackout_dates">
        <table>
          <tr>
            <th>Blackout Dates<span>(Format: YYYY-MM-DD)</span></th>
          </tr>
          <tr>
            <?php $blackout_days=$shopify_datas[$delivery]['blackout'];
					$commaBlackList = implode(',', $blackout_days);
					?>
            <td><textarea name="black_day" id="black_day" rows="4"
                style="width:100%"><?php echo $commaBlackList; ?></textarea></td>
          </tr>
          <input type="hidden" value="<?php echo $detail; ?>" name="delivery_groupid" />
          <tr>
            <td><input type="submit" value="Save" name="delivery_blackout" id="delivery_blackout"></td>
          </tr>
        </table>
      </form>
    </div>
  </section>

  <section class="content">
    <h2>Regions</h2>
    <?php if($successdiv_region){ ?>
    <div class="success_region">
      <h3 style="color:green">Region Successfully Saved.</h3>
    </div>
    <?php } ?>
    <div class="region_delivery">
      <form method="POST" action="<?php echo base_url('delivery/deliverdetail').'/'.$detail; ?>" name="region_delivery">
        <table>
          <tr>
            <th colspan="2">Select Region</th>
          </tr>
          <tr>
            <td>Region</td>
            <td>
              <select name="select_region" id="select_region" required>
                <option value="">------</option>
                <?php if($region){ 
								$delivery_region=$shopify_datas[$delivery]['regionname'];
								$region_list=json_decode($region, true);?>
                <?php foreach($region_list as $key=>$value){ ?>
                <option value="<?php echo $key; ?>" <?php if($key==$delivery_region){ echo 'selected'; } ?>>
                  <?php echo $value; ?></option>
                <?php }?>
                <?php } ?>
              </select>
            </td>
          </tr>
          <input type="hidden" value="<?php echo $detail; ?>" name="delivery_groupid" />
          <tr>
            <td><input type="submit" value="Save" name="delivery_region" id="delivery_region"></td>
          </tr>
        </table>
      </form>
    </div>
  </section>
</div>