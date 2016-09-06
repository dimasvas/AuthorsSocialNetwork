QUnit.test("hasItems", function( assert ) {
  assert.strictEqual( false,  core.hasItems([]), 'false and false has the same value and type');
});

QUnit.test("hasItems", function( assert ) {
  assert.strictEqual( true,  core.hasItems([1,2]), 'true and true has the same value and type');
});

QUnit.test("getBoolean", function( assert ) {
  assert.strictEqual( true,  core.getBoolean('true'), 'true and true has the same value and type');
});

QUnit.test("showElement", function( assert ) {
    
    var element  = $('<li class="hidden"></li>');
    
    core.showElement(element);
    
    assert.strictEqual(false, element.hasClass('hidden'), '');
});

QUnit.test("hideElement", function( assert ) {
    
    var element  = $('<li></li>');
    
    core.hideElement(element);
    
    assert.strictEqual(true, element.hasClass('hidden'), '');
});

QUnit.test("emptyElement", function( assert ) {
    
    var element  = $('<li><p>content</p></li>');
    
    assert.strictEqual(false, element.is(':empty'), '');
    
    core.emptyElement(element);
    
    assert.strictEqual(true, element.is(':empty'), '');
});