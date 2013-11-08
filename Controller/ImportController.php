<?php
class ImportController extends AppController {
	public $name = 'Import';
	public $uses = array('Value', 'Location');

	public function beforeFilter() {
		$this->layout = 'import';
	}

	public function in_county_income_tax() {
		$data = "
			18001	1.124%
			18003	1.000%
			18005	1.250%
			18007	2.290%
			18009	1.360%
			18011	1.000%
			18013	2.200%
			18015	1.550%
			18017	2.500%
			18019	2.000%
			18021	2.250%
			18023	2.000%
			18025	1.000%
			18027	1.750%
			18029	0.600%
			18031	1.330%
			18033	1.500%
			18035	1.050%
			18037	1.000%
			18039	1.500%
			18041	2.370%
			18043	1.150%
			18045	1.100%
			18047	1.250%
			18049	1.930%
			18051	0.500%
			18053	2.250%
			18055	1.000%
			18057	1.000%
			18059	1.550%
			18061	1.000%
			18063	1.400%
			18065	1.250%
			18067	1.600%
			18069	1.600%
			18071	1.600%
			18073	3.050%
			18075	2.450%
			18077	0.350%
			18079	1.250%
			18081	1.000%
			18083	1.100%
			18085	1.000%
			18087	1.400%
			18091	0.950%
			18093	1.750%
			18095	1.750%
			18097	1.620%
			18099	1.250%
			18101	1.000%
			18103	2.540%
			18105	1.050%
			18107	2.100%
			18109	2.720%
			18111	1.000%
			18113	1.500%
			18115	1.000%
			18117	1.250%
			18119	1.300%
			18121	2.300%
			18123	1.060%
			18125	0.400%
			18127	0.500%
			18129	1.000%
			18131	3.130%
			18133	1.500%
			18135	1.500%
			18137	1.380%
			18139	1.500%
			18141	1.750%
			18143	1.410%
			18145	1.250%
			18147	0.800%
			18149	1.060%
			18151	1.790%
			18153	0.300%
			18155	1.000%
			18157	1.100%
			18159	1.330%
			18161	1.500%
			18163	1.000%
			18165	0.100%
			18167	1.250%
			18169	2.900%
			18171	2.120%
			18173	0.500%
			18175	1.500%
			18177	1.500%
			18179	2.100%
			18181	1.320%
			18183	1.233%
		";
		$data_split = explode("\n", $data);
		foreach ($data_split as $row) {
			$row = trim($row);
			if (empty($row)) {
				continue;
			}
			$row_split = explode("\t", $row);
			$loc_type = 'county';
			$fips = trim($row_split[0]);
			$loc_id = $this->Location->getCountyIDFromFips($fips);
			$category_id = 1; // county income tax
			$value = trim(str_replace('%', '', $row_split[1]));
			$datum = compact('loc_type', 'loc_id', 'category_id', 'value');
			if ($error_code = $this->Value->add($datum)) {
				echo "Error importing data. Row: $row. Error code: $error_code.";
				break;
			}
		}
		if (! isset($error_code) || ! $error_code) {
			echo "Import completed.";
		}
		$this->render('/import/index', 'import');
	}

	public function in_state_income_tax() {
		$loc_type = 'state';
		$loc_id = 14;
		$category_id = 2; // state income tax
		$value = 3.40;
		$datum = compact('loc_type', 'loc_id', 'category_id', 'value');
		if ($error_code = $this->Value->add($datum)) {
			echo "Error importing data. Error code: $error_code.";
			return $this->render('/import/index', 'import');
		}

		if (! isset($error_code) || ! $error_code) {
			echo "Import completed.";
		}
		$this->render('/import/index', 'import');
	}

	public function il_state_income_tax() {
		$loc_type = 'state';
		$loc_id = 13;
		$category_id = 2; // state income tax
		$value = 5;
		$datum = compact('loc_type', 'loc_id', 'category_id', 'value');
		if ($error_code = $this->Value->add($datum)) {
			echo "Error importing data. Error code: $error_code.";
			return $this->render('/import/index', 'import');
		}

		if (! isset($error_code) || ! $error_code) {
			echo "Import completed.";
		}
		$this->render('/import/index', 'import');
	}

	public function in_property_tax() {
		$data = "
			18001	2.220%
			18003	2.610%
			18005	2.110%
			18007	1.850%
			18009	2.870%
			18011	1.870%
			18013	0.860%
			18015	1.710%
			18017	2.810%
			18019	2.110%
			18021	1.850%
			18023	2.120%
			18025	2.830%
			18027	2.390%
			18029	1.940%
			18031	1.630%
			18033	1.720%
			18035	3.240%
			18037	1.930%
			18039	2.410%
			18041	3.260%
			18043	1.890%
			18045	1.850%
			18047	1.350%
			18049	1.640%
			18051	2.130%
			18053	2.590%
			18055	2.340%
			18057	1.910%
			18059	2.280%
			18061	1.400%
			18063	2.420%
			18065	2.530%
			18067	2.570%
			18069	2.420%
			18071	1.800%
			18073	1.180%
			18075	2.420%
			18077	2.140%
			18079	2.270%
			18081	2.250%
			18083	2.470%
			18085	1.270%
			18087	1.220%
			18089	3.290%
			18091	0.840%
			18093	2.800%
			18095	3.540%
			18097	2.540%
			18099	1.550%
			18101	1.930%
			18103	2.360%
			18105	1.600%
			18107	2.110%
			18109	1.440%
			18111	2.050%
			18113	1.800%
			18115	0.820%
			18117	1.630%
			18119	2.080%
			18121	1.590%
			18123	2.480%
			18125	2.110%
			18127	1.950%
			18129	1.660%
			18131	1.510%
			18133	1.710%
			18135	2.270%
			18137	1.460%
			18139	1.970%
			18141	3.280%
			18143	2.220%
			18145	1.870%
			18147	1.540%
			18149	1.840%
			18151	1.080%
			18153	2.230%
			18155	1.190%
			18157	2.050%
			18159	1.830%
			18161	2.200%
			18163	2.150%
			18165	1.970%
			18167	2.750%
			18169	1.960%
			18171	1.560%
			18173	1.640%
			18175	2.200%
			18177	2.680%
			18179	1.500%
			18181	1.440%
			18183	1.650%
		";
		$data_split = explode("\n", $data);
		foreach ($data_split as $row) {
			$row = trim($row);
			if (empty($row)) {
				continue;
			}
			$row_split = explode("\t", $row);
			$loc_type = 'county';
			$fips = trim($row_split[0]);
			$loc_id = $this->Location->getCountyIDFromFips($fips);
			$category_id = 3; // property tax
			$value = trim(str_replace('%', '', $row_split[1]));
			$datum = compact('loc_type', 'loc_id', 'category_id', 'value');
			if ($error_code = $this->Value->add($datum, true)) {
				echo "Error importing data. Row: $row. Error code: $error_code.";
				break;
			}
		}
		if (! isset($error_code) || ! $error_code) {
			echo "Import completed.";
		}
		$this->render('/import/index', 'import');
	}

	public function il_property_tax() {
		$data = "
			17001	1.54%
			17003	1.08%
			17005	1.44%
			17007	1.84%
			17009	1.46%
			17011	1.81%
			17013	1.21%
			17015	1.90%
			17017	1.76%
			17019	1.91%
			17021	1.58%
			17023	1.51%
			17025	1.07%
			17027	1.63%
			17029	1.89%
			17031	1.29%
			17033	1.30%
			17035	1.49%
			17037	2.12%
			17039	1.62%
			17041	1.80%
			17043	1.59%
			17045	1.44%
			17047	1.34%
			17049	1.50%
			17051	1.39%
			17053	1.94%
			17055	1.21%
			17057	1.70%
			17059	0.75%
			17061	1.31%
			17063	1.81%
			17065	0.94%
			17067	1.54%
			17069	0.63%
			17071	1.50%
			17073	1.87%
			17075	1.90%
			17077	1.62%
			17079	1.27%
			17081	1.30%
			17083	1.38%
			17085	1.52%
			17087	1.07%
			17089	1.97%
			17091	1.87%
			17093	2.02%
			17095	1.80%
			17097	2.08%
			17099	2.00%
			17101	1.32%
			17103	1.82%
			17105	2.17%
			17107	1.79%
			17109	1.77%
			17111	1.45%
			17113	1.57%
			17115	1.93%
			17117	1.45%
			17119	1.67%
			17121	1.43%
			17123	1.83%
			17125	1.81%
			17127	1.26%
			17129	1.80%
			17131	1.78%
			17133	1.59%
			17135	1.57%
			17137	1.74%
			17139	1.90%
			17141	1.84%
			17143	1.92%
			17145	1.42%
			17147	1.59%
			17149	1.35%
			17151	1.26%
			17153	0.82%
			17155	1.52%
			17157	1.29%
			17159	1.37%
			17161	1.94%
			17163	1.71%
			17165	1.22%
			17167	1.82%
			17169	1.71%
			17171	1.45%
			17173	1.50%
			17175	1.82%
			17177	2.23%
			17179	1.80%
			17181	1.17%
			17183	1.54%
			17185	1.49%
			17187	1.62%
			17189	1.66%
			17191	1.17%
			17193	1.18%
			17195	1.99%
			17197	1.94%
			17199	1.30%
			17201	2.31%
			17203	1.90%
		";
		$data_split = explode("\n", $data);
		foreach ($data_split as $row) {
			$row = trim($row);
			if (empty($row)) {
				continue;
			}
			$row_split = explode("\t", $row);
			$loc_type = 'county';
			$fips = trim($row_split[0]);
			$loc_id = $this->Location->getCountyIDFromFips($fips);
			$category_id = 3; // property tax
			$value = trim(str_replace('%', '', $row_split[1]));
			$datum = compact('loc_type', 'loc_id', 'category_id', 'value');
			if ($error_code = $this->Value->add($datum)) {
				echo "Error importing data. Row: $row. Error code: $error_code.";
				break;
			}
		}
		if (! isset($error_code) || ! $error_code) {
			echo "Import completed.";
		}
		$this->render('/import/index', 'import');
	}

	public function sandbox() {
		$test = "
			17001	Adams
			17003	Alexander
			17005	Bond
			17007	Boone
			17009	Brown
			17011	Bureau
			17013	Calhoun
			17015	Carroll
			17017	Cass
			17019	Champaign
			17021	Christian
			17023	Clark
			17025	Clay
			17027	Clinton
			17029	Coles
			17031	Cook
			17033	Crawford
			17035	Cumberland
			17037	DeKalb
			17039	De Witt
			17041	Douglas
			17043	DuPage
			17045	Edgar
			17047	Edwards
			17049	Effingham
			17051	Fayette
			17053	Ford
			17055	Franklin
			17057	Fulton
			17059	Gallatin
			17061	Greene
			17063	Grundy
			17065	Hamilton
			17067	Hancock
			17069	Hardin
			17071	Henderson
			17073	Henry
			17075	Iroquois
			17077	Jackson
			17079	Jasper
			17081	Jefferson
			17083	Jersey
			17085	Jo Daviess
			17087	Johnson
			17089	Kane
			17091	Kankakee
			17093	Kendall
			17095	Knox
			17097	Lake
			17099	LaSalle
			17101	Lawrence
			17103	Lee
			17105	Livingston
			17107	Logan
			17109	McDonough
			17111	McHenry
			17113	McLean
			17115	Macon
			17117	Macoupin
			17119	Madison
			17121	Marion
			17123	Marshall
			17125	Mason
			17127	Massac
			17129	Menard
			17131	Mercer
			17133	Monroe
			17135	Montgomery
			17137	Morgan
			17139	Moultrie
			17141	Ogle
			17143	Peoria
			17145	Perry
			17147	Piatt
			17149	Pike
			17151	Pope
			17153	Pulaski
			17155	Putnam
			17157	Randolph
			17159	Richland
			17161	Rock Island
			17163	St. Clair
			17165	Saline
			17167	Sangamon
			17169	Schuyler
			17171	Scott
			17173	Shelby
			17175	Stark
			17177	Stephenson
			17179	Tazewell
			17181	Union
			17183	Vermilion
			17185	Wabash
			17187	Warren
			17189	Washington
			17191	Wayne
			17193	White
			17195	Whiteside
			17197	Will
			17199	Williamson
			17201	Winnebago
			17203	Woodford
		";
		$test_split = explode("\n", $test);
		foreach ($test_split as $row) {
			$row = trim($row);
			if (empty($row)) {
				continue;
			}
			$cells = explode("\t", $row);
			echo "INSERT INTO counties (state, name, fips) VALUES ('IL', '$cells[1]', '$cells[0]');<br />";
		}
		$this->render('/import/index', 'import');
	}
}