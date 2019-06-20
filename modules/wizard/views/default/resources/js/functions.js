function makerand(possible, count) {
  var text = "";

  for (var i = 0; i < count; i++)
    text += possible.charAt(Math.floor(Math.random() * possible.length));

  return text;
}