<?php
namespace NextFerry;
use PHPUnit\Framework\TestCase;

require_once "lib/next-ferry/index.php";

class TestNextFerryLib extends TestCase {

    public function testCanBeCreatedFromValidEmailAddress(){
        $this->assertTrue( true );
    }

    public function test_get_timetable_from_html_empty() {
      $html = "";

      $timetable = get_timetable_from_html( $html );
      $this->assertEquals( $timetable, [] );
    }

    public function test_get_timetable_from_bowen_html() {
      $html = '<table class="table table-condensed table-striped table-hover"> 					<tbody> 						<tr class="schedule_header"> 							<th colspan="2"><strong>Leave Bowen Island </strong>(Snug Cove)</th> 						</tr> 		<tr> 			<td>5:20&nbsp;am</td> 			<td>Daily except  Sun and Apr 10			</td> 		</tr> 		<tr> 			<td>6:20&nbsp;am</td> 			<td>Daily			</td> 		</tr> 		<tr> 			<td>7:30&nbsp;am</td> 			<td>Daily			</td> 		</tr> 		<tr> 			<td>8:35&nbsp;am</td> 			<td>Daily			</td> 		</tr> 		<tr> 			<td>9:40&nbsp;am</td> 			<td>Daily			</td> 		</tr> 		<tr> 			<td>10:50&nbsp;am</td> 			<td>Daily			</td> 		</tr> 		<tr> 			<td>12:00&nbsp;pm</td> 			<td>Daily			</td> 		</tr> 		<tr> 			<td>1:10&nbsp;pm</td> 			<td>Daily			</td> 		</tr> 		<tr> 			<td>2:55&nbsp;pm</td> 			<td>Daily			</td> 		</tr> 		<tr class="exception" href="#exc11" data-toggle="collapse"> 			<td>4:00&nbsp;pm</td> 			<td>Daily&nbsp;except Wed are (DC) <a href="#exc11" class="btn btn-xs btn-warning" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="exc11"><span class="fa fa-exclamation-circle" aria-hidden="true"></span> Availability</a> <div class="collapse" id="exc11"> <table class="NT"> <tr valign="top"><td>DC</td><td>Wednesday sailings will be replaced by Dangerous Cargo sailings. No other passengers permitted.</td></tr> </table> </div>			</td> 		</tr> 		<tr> 			<td>5:10&nbsp;pm</td> 			<td>Daily			</td> 		</tr> 		<tr> 			<td>6:15&nbsp;pm</td> 			<td>Daily			</td> 		</tr> 		<tr> 			<td>7:25&nbsp;pm</td> 			<td>Daily except Sat			</td> 		</tr> 		<tr> 			<td>8:30&nbsp;pm</td> 			<td>Daily			</td> 		</tr> 		<tr> 			<td>9:30&nbsp;pm</td> 			<td>Daily			</td> 		</tr> 		<tr> 			<td>10:30&nbsp;pm</td> 			<td>Daily			</td> 		</tr> 	</tbody> </table>';

      $timetable = get_timetable_from_html( $html );
      $expected_timetable = array(
              "5:20 am - Daily except Sun and Apr 10",
              "6:20 am - Daily",
              "7:30 am - Daily",
              "8:35 am - Daily",
              "9:40 am - Daily",
              "10:50 am - Daily",
              "12:00 pm - Daily",
              "1:10 pm - Daily",
              "2:55 pm - Daily",
              "4:00 pm - Daily except Wed are",
              "5:10 pm - Daily",
              "6:15 pm - Daily",
              "7:25 pm - Daily except Sat",
              "8:30 pm - Daily",
              "9:30 pm - Daily",
              "10:30 pm - Daily",
      );
      $this->assertEquals( $timetable[0], $expected_timetable[0] );
      $this->assertEquals( $timetable, $expected_timetable );
    }

    function test_get_time_from_row() {
      $this->assertEquals( strtotime( "Today 8:35 pm" ), get_time_from_row( "8:35 pm - Daily", time() ) );
      $this->assertEquals( null, get_time_from_row( "8:35 pm - Daily except Fri", strtotime( "Friday 8:35pm" ) ) );
      $this->assertEquals( null, get_time_from_row( "4:00 pm - Daily except Wed", strtotime( "Wednesday 8:35pm" ) ) );
      $this->assertEquals( null, get_time_from_row( "5:20 am - Daily except Sun and Apr 10", strtotime( "Apr 10 8:35pm" ) ) );
      $this->assertEquals( null, get_time_from_row( "5:20 am - Daily except Sun and Apr 10", strtotime( "Sunday 8:35pm" ) ) );
      $this->assertEquals( strtotime( "Sunday 5:20 am" ), get_time_from_row( "5:20 am - Sun and Wed", strtotime( "Sunday 8:35pm" ) ) );
      $this->assertEquals( strtotime( "April 3rd 5:20 am" ), get_time_from_row( "5:20 am - Apr 3", strtotime( "April 3rd 8:35pm" ) ) );
    }

    function test_get_next_ferry_time_from_timetable() {
      $timetable = array(
          "5:20 am - Daily except Sun and Apr 10",
          "6:20 am - Daily",
          "7:30 am - Daily",
          "8:35 am - Daily",
          "9:40 am - Daily",
          "10:50 am - Daily",
          "12:00 pm - Daily",
          "1:10 pm - Daily",
          "2:55 pm - Daily",
          "4:00 pm - Daily except Wed are",
          "5:10 pm - Daily",
          "6:15 pm - Daily",
          "7:25 pm - Daily except Sat",
          "8:30 pm - Daily",
          "9:30 pm - Daily",
          "10:30 pm - Daily",
      );
      // First ferry
      $this->assertEquals( strtotime( "Friday 5:20 am" ), get_next_ferry_time_from_timetable( $timetable, strtotime( "Friday 4:35am" ) ) );
      $this->assertEquals( strtotime( "Saturday 5:20 am" ), get_next_ferry_time_from_timetable( $timetable, strtotime( "Friday 11:30 pm" ) ) );
      $this->assertEquals( strtotime( "Wednesday 5:10 pm" ), get_next_ferry_time_from_timetable( $timetable, strtotime( "Wednesday 3:55 pm" ) ) );

    }
}
