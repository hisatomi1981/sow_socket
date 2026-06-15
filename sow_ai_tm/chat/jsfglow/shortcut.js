//ショートカットキー
function deletecommanad(){
	myDiagram.commandHandler.deleteSelection();
}
function undocommanad(){
	myDiagram.commandHandler.undo();
}
function redocommanad(){
	myDiagram.commandHandler.redo();
}
function allselectcommanad(){
	myDiagram.commandHandler.selectAll();
}