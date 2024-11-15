<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Tanda Terima Bahan</title>

	<!-- Report Office2013 style -->
	<link href="{{ asset('plugins/stimulsoft/css/stimulsoft.viewer.office2013.whiteteal.css') }}" rel="stylesheet">

	<!-- Stimusloft Reports.JS -->
	<script src="{{ asset('plugins/stimulsoft/scripts/stimulsoft.reports.js') }}" type="text/javascript"></script>
	<script src="{{ asset('plugins/stimulsoft/scripts/stimulsoft.viewer.js') }}" type="text/javascript"></script>
	
    <script type="text/javascript">   
		    
		var options = new Stimulsoft.Viewer.StiViewerOptions();
		options.appearance.fullScreenMode = true;
        options.exports.showExportToCsv = false;
        options.exports.showExportToDocument = false;
        options.exports.showExportToHtml = false;
        options.exports.showExportToHtml5 = false;
		options.toolbar.showSendEmailButton = false;
        options.toolbar.showOpenButton = false;
        options.toolbar.showFullScreenButton = false;
        options.toolbar.showAboutButton = false;
        options.toolbar.viewMode = Stimulsoft.Viewer.StiWebViewMode.Continuous;

        Stimulsoft.Base.StiLicense.key = "6vJhGtLLLz2GNviWmUTrhSqnOItdDwjBylQzQcAOiHkcgIvwL0jnpsDqRpWg5FI5kt2G7A0tYIcUygBh1sPs7plofUOqPB1a4HBIXJB621mau2oiAIj+ysU7gKUXfjn/D5BocmduNB+ZMiDGPxFrAp3PoD0nYNkkWh8r7gBZ1v/JZSXGE3bQDrCQCNSy6mgby+iFAMV8/PuZ1z77U+Xz3fkpbm6MYQXYp3cQooLGLUti7k1TFWrnawT0iEEDJ2iRcU9wLqn2g9UiWesEZtKwI/UmEI2T7nv5NbgV+CHguu6QU4WWzFpIgW+3LUnKCT/vCDY+ymzgycw9A9+HFSzARiPzgOaAuQYrFDpzhXV+ZeX31AxWlnzjDWqpfluygSNPtGul5gyNt2CEoJD1Yom0VN9fvRonYsMsimkFFx2AwyVpPcs+JfVBtpPbTcZscnzUdmiIvxv8Gcin6sNSibM6in/uUKFt3bVgW/XeMYa7MLGF53kvBSwi78poUDigA2n12SmghLR0AHxyEDIgZGOTbNI33GWu7ZsPBeUdGu55R8w=";
		Stimulsoft.Base.Localization.StiLocalization.setLocalizationFile("{{ asset('plugins/stimulsoft/scripts/id.xml') }}");
		
		var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);
		
		// Process SQL data source
		viewer.onBeginProcessData = function (event, callback) {
			
		}
		
		// Manage export settings on the server side
		viewer.onBeginExportReport = function (args) {
			args.fileName = "TandaTerimaBahan";
		}
		
        // Load and show report
        var dataSet = new Stimulsoft.System.Data.DataSet("DataSet");
		dataSet.readJson({!! $peserta !!});

		var report = new Stimulsoft.Report.StiReport();
        report.loadFile("{{ asset('plugins/stimulsoft/reports/umum/ttbahan.mrt') }}");
        report.regData(dataSet.dataSetName, "", dataSet);
		report.dictionary.variables.getByName("var_tipe").valueObject="{{ $jadwal->tipe }}";
		report.dictionary.variables.getByName("var_jadwal").valueObject="{{ $jadwal->nama }}";
		report.dictionary.variables.getByName("var_kelas").valueObject="{{ $jadwal->kelas }}";
		report.dictionary.variables.getByName("var_tahun").valueObject="{{ $jadwal->tahun }}";
        report.dictionary.variables.getByName("var_tanggal").valueObject="{{ getHari($tanggal) }}, {{formatTanggal($tanggal)}}";
		
		viewer.report = report;
		
		function onLoad() {
			viewer.renderHtml("viewerContent");
		}
	</script>
	</head>
<body onload="onLoad();">
	<div id="viewerContent"></div>
</body>
</html>
