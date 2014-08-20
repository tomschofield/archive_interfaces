/**!
 * AngularJS mouse drag directive
 * @author  Ozan Tunca  <ozan@ozantunca.org>
 * @version 0.1.0
 */
(function() {

  var ngMousedrag = angular.module('ngMouseDrag', []);
  ngMousedrag.directive('ngMousedrag', ['$document', function ngMousedrag($document) {
    return {
      restrict: 'A',
      link: function (scope, element, attrs) {
        var endTypes = 'touchend mouseup'
          , moveTypes = 'touchmove mousemove'
          , startTypes = 'touchstart mousedown'
          , startX, startY;

        element.bind(startTypes, function startDrag(e) {
          e.preventDefault();
          startX = e.pageX;
          startY = e.pageY;

          $document.bind(moveTypes, function (e) {
            e.dragX = e.pageX - startX;
            e.dragY = e.pageY - startY;
            e.startX = startX;
            e.startY = startY;
            scope.$event = e;
            scope.$eval(attrs.ngMousedrag);
          });

          $document.bind(endTypes, function () {
            $document.unbind(moveTypes);
          });
        });
      }
    };
  }]);

})();