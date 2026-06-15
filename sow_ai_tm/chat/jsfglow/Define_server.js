  function init() {
	var GO = go.GraphObject.make; // for conciseness in defining templates

	myDiagram = GO(
	  go.Diagram,
	  "myDiagramDiv", // must name or refer to the DIV HTML element
	  {
		allowDrop: true, // must be true to accept drops from the Palette
		LinkDrawn: showLinkLabel, // this DiagramEvent listener is defined below
		LinkRelinked: showLinkLabel,
		"undoManager.isEnabled": true, // enable undo & redo
	  }
	);

	//ドラッグドロップした時、パラメータを修正した時、何もないところでクリックした時に発生
	myDiagram.addDiagramListener(
	  "ChangedSelection",
	  function (diagramEvent) {		
		var idrag = document.getElementById("infoDraggable");
		//パラメータ設定をすべて非表示に
		para_label_notdisplay();
		idrag.style.display = "none";
		idrag.style.width = "";
		idrag.style.height = "";
	  }
	);

    // Make all ports on a node visible when the mouse is over the node
    function showPorts(node, show) {
		var diagram = node.diagram;
		if (!diagram || diagram.isReadOnly || !diagram.allowLink) return;
		node.ports.each(function (port) {
		  port.stroke = (show ? "white" : null);
		});
	  }
	function nodeStyle() {
	  return [
		new go.Binding("location", "loc", go.Point.parse).makeTwoWay(
		  go.Point.stringify
		),
		{
		  locationSpot: go.Spot.Center,
		  mouseEnter: function (e, obj) {
			showPorts(obj.part, true);
		  },
		  mouseLeave: function (e, obj) {
			showPorts(obj.part, false);
		  }
		},
	  ];
	}

	function makePort(name, align, spot, output, input) {
	  var horizontal = align.equals(go.Spot.Top) || align.equals(go.Spot.Bottom);
	  return GO(go.Shape, {
		fill: "transparent", // changed to a color in the mouseEnter event handler
		strokeWidth: 0, // no stroke
		width: horizontal ? NaN : 8, // if not stretching horizontally, just 8 wide
		height: !horizontal ? NaN : 8, // if not stretching vertically, just 8 tall
		alignment: align, // align the port on the main Shape
		name: "PortObj",
		stretch: horizontal
		  ? go.GraphObject.Horizontal
		  : go.GraphObject.Vertical,
		portId: name, // declare this object to be a "port"
		fromSpot: spot, // declare where links may connect at this port
		fromLinkable: output, // declare whether the user may draw links from here
		toSpot: spot, // declare where links may connect at this port
		toLinkable: input, // declare whether the user may draw links to here
		cursor: "pointer", // show a different cursor to indicate potential link point
		mouseEnter: function (e, port) {
		  // the PORT argument will be this Shape
		  if (!e.diagram.isReadOnly) port.fill = "rgba(255,0,255,0.5)";
		},
		mouseLeave: function (e, port) {
		  port.fill = "transparent";
		},
	  });
	}

	function textStyle() {
	  return {
		//font: "bold 11pt Lato, Helvetica, Arial, sans-serif",
		font: "10pt Lato, Helvetica, Arial, sans-serif",
		stroke: "#000000",
	  };
	}
    //メッセージ
	myDiagram.nodeTemplateMap.add(
	  "server_Message",
	  GO(
		go.Node,
		"Table",
		nodeStyle(),
		{ locationSpot: go.Spot.Center },
		new go.Binding("location", "loc", go.Point.parse).makeTwoWay(
		  go.Point.stringify
		),
		GO(
		  go.Panel,
		  "Auto",
		  GO(
			go.Shape,
			"Rectangle",
			{
			  desiredSize: new go.Size(100, 50),
			  fill: "#afd9f0",
			  stroke: "#000000",
			  strokeWidth: 1.5,
			},
			new go.Binding("figure", "figure"),
			new go.Binding("fill", "fill"),
			new go.Binding("stroke", "stroke")
		  ),
		  GO(
			go.TextBlock,
			textStyle(),
			{
			  margin: 8,
			  maxSize: new go.Size(100, NaN),
			  wrap: go.TextBlock.WrapFit,
			  editable: false,
			},
			new go.Binding("text").makeTwoWay()
		  )
		),
		makePort("上", go.Spot.Top, go.Spot.TopSide, false, true),
		makePort("下", go.Spot.Bottom, go.Spot.BottomSide, true, false),
		{
		  click: function (e, obj) {
			showInspectData(e, obj);
		  },
		}
	  )
	);
    //エラーを返信
	myDiagram.nodeTemplateMap.add(
	  "server_Error",
	  GO(
		go.Node,
		"Table",
		nodeStyle(),
		{ locationSpot: go.Spot.Center },
		new go.Binding("location", "loc", go.Point.parse).makeTwoWay(
		  go.Point.stringify
		),
		GO(
		  go.Panel,
		  "Auto",
		  GO(
			go.Shape,
			"Rectangle",
			{
			  desiredSize: new go.Size(100, 50),
			  fill: "#fadcfa",
			  stroke: "#000000",
			  strokeWidth: 1.5,
			},
			new go.Binding("figure", "figure"),
			new go.Binding("fill", "fill"),
			new go.Binding("stroke", "stroke")
		  ),
		  GO(
			go.TextBlock,
			textStyle(),
			{
			  margin: 8,
			  maxSize: new go.Size(100, NaN),
			  wrap: go.TextBlock.WrapFit,
			  editable: false,
			},
			new go.Binding("text").makeTwoWay()
		  )
		),
		makePort("上", go.Spot.Top, go.Spot.TopSide, false, true),
		makePort("下", go.Spot.Bottom, go.Spot.BottomSide, true, false),
		{
		  click: function (e, obj) {
			showInspectData(e, obj);
		  },
		}
	  )
	);
    //AI
	myDiagram.nodeTemplateMap.add(
	  "server_AI",
	  GO(
		go.Node,
		"Table",
		nodeStyle(),
		{ locationSpot: go.Spot.Center },
		new go.Binding("location", "loc", go.Point.parse).makeTwoWay(
		  go.Point.stringify
		),
		GO(
		  go.Panel,
		  "Auto",
		  GO(
			go.Shape,
			"Rectangle",
			{
			  desiredSize: new go.Size(100, 50),
			  fill: "#95abbd",
			  stroke: "#000000",
			  strokeWidth: 1.5,
			},
			new go.Binding("figure", "figure"),
			new go.Binding("fill", "fill"),
			new go.Binding("stroke", "stroke")
		  ),
		  GO(
			go.TextBlock,
			textStyle(),
			{
			  margin: 8,
			  maxSize: new go.Size(100, NaN),
			  wrap: go.TextBlock.WrapFit,
			  editable: false,
			},
			new go.Binding("text").makeTwoWay()
		  )
		),
		makePort("上", go.Spot.Top, go.Spot.TopSide, false, true),
		makePort("下", go.Spot.Bottom, go.Spot.BottomSide, true, false),
		{
		  click: function (e, obj) {
			showInspectData(e, obj);
		  },
		}
	  )
	);
    //変数
	myDiagram.nodeTemplateMap.add(
	  "server_Variable",
	  GO(
		go.Node,
		"Table",
		nodeStyle(),
		{ locationSpot: go.Spot.Center },
		new go.Binding("location", "loc", go.Point.parse).makeTwoWay(
		  go.Point.stringify
		),
		GO(
		  go.Panel,
		  "Auto",
		  GO(
			go.Shape,
			"Rectangle",
			{
			  desiredSize: new go.Size(100, 50),
			  fill: "#9e9065",
			  stroke: "#000000",
			  strokeWidth: 1.5,
			},
			new go.Binding("figure", "figure"),
			new go.Binding("fill", "fill"),
			new go.Binding("stroke", "stroke")
		  ),
		  GO(
			go.TextBlock,
			textStyle(),
			{
			  margin: 8,
			  maxSize: new go.Size(100, NaN),
			  wrap: go.TextBlock.WrapFit,
			  editable: false,
			},
			new go.Binding("text").makeTwoWay()
		  )
		),
		makePort("上", go.Spot.Top, go.Spot.TopSide, false, true),
		makePort("下", go.Spot.Bottom, go.Spot.BottomSide, true, false),
		{
		  click: function (e, obj) {
			showInspectData(e, obj);
		  },
		}
	  )
	);
    //設定
	myDiagram.nodeTemplateMap.add(
	  "server_Setup",
	  GO(
		go.Node,
		"Table",
		nodeStyle(),
		{ locationSpot: go.Spot.Center },
		new go.Binding("location", "loc", go.Point.parse).makeTwoWay(
		  go.Point.stringify
		),
		GO(
		  go.Panel,
		  "Auto",
		  GO(
			go.Shape,
			"Rectangle",
			{
			  desiredSize: new go.Size(100, 50),
			  fill: "#ff8c1a",
			  stroke: "#000000",
			  strokeWidth: 1.5,
			},
			new go.Binding("figure", "figure"),
			new go.Binding("fill", "fill"),
			new go.Binding("stroke", "stroke")
		  ),
		  GO(
			go.TextBlock,
			textStyle(),
			{
			  margin: 8,
			  maxSize: new go.Size(100, NaN),
			  wrap: go.TextBlock.WrapFit,
			  editable: false,
			},
			new go.Binding("text").makeTwoWay()
		  )
		),
		makePort("上", go.Spot.Top, go.Spot.TopSide, false, true),
		makePort("下", go.Spot.Bottom, go.Spot.BottomSide, true, false),
		{
		  click: function (e, obj) {
			showInspectData(e, obj);
		  },
		}
	  )
	);
    //条件分岐
	myDiagram.nodeTemplateMap.add(
	  "server_Conditional",
	  GO(
		go.Node,
		"Table",
		nodeStyle(),
		{ locationSpot: go.Spot.Center },
		new go.Binding("location", "loc", go.Point.parse).makeTwoWay(
		  go.Point.stringify
		),
		GO(
		  go.Panel,
		  "Auto",
		  GO(
			go.Shape,
			"Diamond",
			{
			  desiredSize: new go.Size(150, 70),
			  fill: "#ee53b3",
			  stroke: "#000000",
			  strokeWidth: 1.5,
			},
			//{ desiredSize: new go.Size(150, 100), fill: "#ee53b3", stroke: "#000000", strokeWidth: 1.5 },
			//{ fill: "#ee53b3", stroke: "#000000", strokeWidth: 1.5 },
			new go.Binding("figure", "figure"),
			new go.Binding("fill", "fill"),
			new go.Binding("stroke", "stroke")
		  ),
		  GO(
			go.TextBlock,
			textStyle(),
			{
			  margin: 8,
			  maxSize: new go.Size(200, NaN),
			  wrap: go.TextBlock.WrapFit,
			  editable: false,
			},
			new go.Binding("text").makeTwoWay()
		  )
		),
		// four named ports, one on each side:
		makePort("上", go.Spot.Top, go.Spot.Top, false, true),
		makePort("右", go.Spot.Right, go.Spot.Right, true, false),
		makePort("下", go.Spot.Bottom, go.Spot.Bottom, true, false),
		{
		  click: function (e, obj) {
			showInspectData(e, obj);
		  },
		}
	  )
	);
    //開始
	myDiagram.nodeTemplateMap.add(
	  "Start",
	  GO(
		go.Node,
		"Table",
		nodeStyle(),
		GO(
		  go.Panel,
		  "Spot",
		  GO(
			go.Shape,
			"Terminator",
			{
			  desiredSize: new go.Size(100, 50),
			  fill: "#FFFFFF",
			  stroke: "#000000",
			  strokeWidth: 1.5,
			},
			new go.Binding("figure", "figure"),
			new go.Binding("fill", "fill"),
			new go.Binding("stroke", "stroke")
		  ),
		  GO(go.TextBlock, "Start", textStyle(), new go.Binding("text"))
		),
		// three named ports, one on each side except the top, all output only:
		makePort("下", go.Spot.Bottom, go.Spot.Bottom, true, false),
		{
		  click: function (e, obj) {
			showInspectData(e, obj);
		  },
		}
	  )
	);
    //終了
	myDiagram.nodeTemplateMap.add(
	  "End",
	  GO(
		go.Node,
		"Table",
		nodeStyle(),
		GO(
		  go.Panel,
		  "Spot",
		  GO(
			go.Shape,
			"Terminator",
			{
			  desiredSize: new go.Size(100, 50),
			  fill: "#FFFFFF",
			  stroke: "#000000",
			  strokeWidth: 1.5,
			},
			new go.Binding("figure", "figure"),
			new go.Binding("fill", "fill"),
			new go.Binding("stroke", "stroke")
		  ),
		  GO(go.TextBlock, "End", textStyle(), new go.Binding("text"))
		),
		// three named ports, one on each side except the bottom, all input only:
		makePort("上", go.Spot.Top, go.Spot.Top, false, true),
		{
		  click: function (e, obj) {
			showInspectData(e, obj);
		  },
		}
	  )
	);

	// replace the default Link template in the linkTemplateMap
	myDiagram.linkTemplate = GO(
	  go.Link, // the whole link panel
	  {
		routing: go.Link.AvoidsNodes,
		curve: go.Link.JumpOver,
		corner: 5,
		toShortLength: 4,
		relinkableFrom: true,
		relinkableTo: true,
		reshapable: true,
		resegmentable: true,
		// mouse-overs subtly highlight links:
		mouseEnter: function (e, link) {
		  link.findObject("HIGHLIGHT").stroke = "rgba(30,144,255,0.2)";
		},
		mouseLeave: function (e, link) {
		  link.findObject("HIGHLIGHT").stroke = "transparent";
		},
		selectionAdorned: false,
	  },
	  new go.Binding("points").makeTwoWay(),
	  GO(
		go.Shape, // the highlight shape, normally transparent
		{
		  isPanelMain: true,
		  strokeWidth: 8,
		  stroke: "transparent",
		  name: "HIGHLIGHT",
		}
	  ),
	  GO(
		go.Shape, // the link path shape
		{ isPanelMain: true, stroke: "gray", strokeWidth: 2 },
		new go.Binding("stroke", "isSelected", function (sel) {
		  return sel ? "dodgerblue" : "gray";
		}).ofObject()
	  ),
	  GO(
		go.Shape, // the arrowhead
		{ toArrow: "standard", strokeWidth: 0, fill: "gray" }
	  ),
	  GO(
		go.Panel,
		"Auto", // the link label, normally not visible
		{
		  visible: false,
		  name: "LABEL",
		  segmentIndex: 2,
		  segmentFraction: 0.5,
		},
		new go.Binding("visible", "visible").makeTwoWay(),
		GO(
		  go.Shape,
		  "RoundedRectangle", // the label shape
		  { fill: "#F8F8F8", strokeWidth: 0 }
		),
		GO(
		  go.TextBlock,
		  "Yes", // the label
		  {
			textAlign: "center",
			name: "TextBlock",
			font: "10pt helvetica, arial, sans-serif",
			stroke: "#333333",
			editable: false,
		  },
		  new go.Binding("text").makeTwoWay()
		)
	  )
	);

	// Make link labels visible if coming out of a "conditional" node.
	// This listener is called by the "LinkDrawn" and "LinkRelinked" DiagramEvents.
	function showLinkLabel(e) {
	  var label = e.subject.findObject("LABEL");
	  if (label !== null) {
		if (e.subject.fromNode.data.category === "server_Conditional") {
		  label.visible = true;
		  if (e.subject.je.fromPort == "右") {
			e.subject.findObject("TextBlock").text = "NO";
		  } else {
			e.subject.findObject("TextBlock").text = "YES";
		  }
		}
	  }
	}

	// temporary links used by LinkingTool and RelinkingTool are also orthogonal:
	myDiagram.toolManager.linkingTool.temporaryLink.routing =
	  go.Link.Orthogonal;
	myDiagram.toolManager.relinkingTool.temporaryLink.routing =
	  go.Link.Orthogonal;

	load(); // load an initial diagram from some JSON text

	// initialize the Palette that is on the left side of the page
	myPalette = GO(
	  go.Palette,
	  "myPaletteDiv", // must name or refer to the DIV HTML element
	  {
		"animationManager.duration": 500,
		nodeTemplateMap: myDiagram.nodeTemplateMap, // share the templates used by myDiagram
		model: new go.GraphLinksModel([
		  // specify the contents of the Palette
		  {
			category: "Start",
			fill: "#FFFFFF",
			stroke: "#000000",
			text: "開始",
			parameter: "0-0-0-0-0-0",
		  },
		  {
			category: "End",
			fill: "#FFFFFF",
			stroke: "#000000",
			text: "終了",
			parameter: "0-0-0-0-0-0",
		  },
		  {
			category: "server_Message",
			fill: "#afd9f0",
			stroke: "#000000",
			text: "メッセージを送る",
			parameter: "0-0-0-0-0-0",
		  },
		  {
			category: "server_Error",
			fill: "#fadcfa",
			stroke: "#000000",
			text: "\"エラー\"を返信",
			parameter: "1-エラー-0-0-0-0",
		  },
		  /*{
			category: "server_AI",
			fill: "#95abbd",
			stroke: "#000000",
			text: "AIで変換する",
			parameter: "0--0-0-0-0",
		  },*/
		  {
			category: "server_Variable",
			fill: "#9e9065",
			stroke: "#000000",
			text: "変数１\"変数\"",
			parameter: "0-変数-0-0-0-0",
		  },
		  {
			category: "server_Setup",
			fill: "#e3e3e3",
			stroke: "#000000",
			text: "ﾊﾟｽﾜｰﾄﾞ1234",
			parameter: "0-1234-0-0-0-0",
		  },
		  {
			category: "server_Conditional",
			fill: "#ffd6b0",
			stroke: "#000000",
			text: "禁止ワードがある?",
			parameter: "0-12-0-0-0-0",
		  },
		]),
	  }
	);

	//パラメータ表示
	function showInspectData(e, obj) {
	  var fromNodeNum = "";
	  var linkIn = obj.findLinksInto(); // get all links out from it
	  var j = 0;
	  while (linkIn.next()) {
		// for each link get the link text and toNode text
		var link = linkIn.value;
		var separater = j == 0 ? "" : ", ";
		fromNodeNum += separater + link.je.from;
		j++;
	  }
	  var toNodeNums = "";
	  var toNodeNum = "";
	  var linkOut = obj.findLinksOutOf();
	  var i = 0;
	  while (linkOut.next()) {
		// for each link get the link text and toNode text
		var link = linkOut.value;
		var separater = i == 0 ? "" : ", ";
		toNodeNums += separater + link.je.to + ":" + link.je.text;
		toNodeNum = link.je.to;
		i++;
	  }
	  // パラメータを表示
	  set_para(obj.je, myDiagram);
		
	  //画面右上に小窓としてパラメータを表示するかどうか	
	  var idrag = document.getElementById("infoDraggable");
	  //表示
	  //idrag.style.display = "block";
	  //非表示
	  idrag.style.display = "none";
	  var options = {
		key: { readOnly: false, show: Inspector.showIfPresent },
		前の番号: { readOnly: false, defaultValue: fromNodeNum },
		次の番号: { readOnly: false, defaultValue: toNodeNum },
		fill: { show: Inspector.showIfPresent, type: "color" },
		stroke: { show: Inspector.showIfPresent, type: "color" },
		parameter: {}
	  };
	  if (obj.category == "server_Conditional") {
		options = {
		  key: { readOnly: false, show: Inspector.showIfPresent },
		  前の番号: { readOnly: false, defaultValue: fromNodeNum },
		  次の番号: { readOnly: false, defaultValue: toNodeNums },
		  fill: { show: Inspector.showIfPresent, type: "color" },
		  stroke: { show: Inspector.showIfPresent, type: "color" },
		  parameter: {}
		};
	  } else if (obj.category == "Start") {
		options = {
		  key: { readOnly: false, show: Inspector.showIfPresent },
		  次の番号: { readOnly: false, defaultValue: toNodeNum },
		  fill: { show: Inspector.showIfPresent, type: "color" },
		  stroke: { show: Inspector.showIfPresent, type: "color" },
		  parameter: {}
		};
	  } else if (obj.category == "End") {
		options = {
		  key: { readOnly: false, show: Inspector.showIfPresent },
		  前の番号: { readOnly: false, defaultValue: fromNodeNum },
		  fill: { show: Inspector.showIfPresent, type: "color" },
		  stroke: { show: Inspector.showIfPresent, type: "color" },
		  parameter: {},
		};
	  }	
	  //ID myInfoにmyDiagramを表示
	  var inspector = new Inspector("myInfo", myDiagram, {
		inspectSelection: false,
		properties: options,
	  });
	  inspector.inspectObject(obj.data);
	}

	$(function () {
	  $("#paletteDraggable")
		.draggable({ handle: "#paletteDraggableHandle" })
		.resizable({
		  // After resizing, perform another layout to fit everything in the palette's viewport
		  stop: function () {
			myPalette.layoutDiagram(true);
		  },
		});

	  // $("#infoDraggable").draggable({ handle: "#infoDraggableHandle" });
	});
  } // end init

  // Show the diagram's model in JSON format that the user may edit
  function save() {
	var textFileAsBlob = new Blob([myDiagram.model.toJson()], {
	  type: "text/plain",
	});
	var fileNameToSaveAs = "flowchart-data.draw"; //filename.extension

	var downloadLink = document.createElement("a");
	downloadLink.download = fileNameToSaveAs;
	downloadLink.innerHTML = "Download File";
	if (window.webkitURL != null) {
	  // Chrome allows the link to be clicked without actually adding it to the DOM.
	  downloadLink.href = window.webkitURL.createObjectURL(textFileAsBlob);
	} else {
	  // Firefox requires the link to be added to the DOM before it can be clicked.
	  downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
	  downloadLink.onclick = destroyClickedElement;
	  downloadLink.style.display = "none";
	  document.body.appendChild(downloadLink);
	}

	downloadLink.click();
  }

  var openFile = function(event) {
	var input = event.target;

	var reader = new FileReader();
	reader.onload = function(){
	  var text = reader.result;
	  var node = document.getElementById('mySavedModel');
	  node.innerText = text;
	  load();
	};
	reader.readAsText(input.files[0]);
	document.uploadForm.reset();
  };

  function load() {
	myDiagram.model = go.Model.fromJson(
	  document.getElementById("mySavedModel").value
	);
  }

  $(function(){
	$('#chartSave').on('click', function(){
		save();
	});
	$('#chartLoad').on('change', function(e){
		openFile(e);
	});
  });
