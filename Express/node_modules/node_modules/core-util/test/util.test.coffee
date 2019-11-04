expect = require('chai').expect
util = require('../index.coffee')



describe 'util.isBlank', ->
  describe 'blank', ->
    it 'is blank (nothing)', ->
      expect(util.isBlank()).to.equal true
      expect(util.isBlank(undefined)).to.equal true
      expect(util.isBlank(null)).to.equal true

    it 'is blank (string)', ->
      expect(util.isBlank('')).to.equal true
      expect(util.isBlank(' ')).to.equal true
      expect(util.isBlank('   ')).to.equal true

    it 'is blank (array)', ->
      expect(util.isBlank([])).to.equal true
      expect(util.isBlank([null])).to.equal true
      expect(util.isBlank([undefined])).to.equal true
      expect(util.isBlank([undefined, null])).to.equal true
      expect(util.isBlank([undefined, null, ''])).to.equal true



  describe 'NOT blank', ->
    it 'is not blank (string)', ->
      expect(util.isBlank('a')).to.equal false
      expect(util.isBlank('   .')).to.equal false

    it 'is not blank (array)', ->
      expect(util.isBlank([1])).to.equal false
      expect(util.isBlank([null, 'value'])).to.equal false
      expect(util.isBlank([null, '   '])).to.equal false

    it 'is not blank (other values)', ->
      expect(util.isBlank(1)).to.equal false
      expect(util.isBlank({})).to.equal false
      expect(util.isBlank(-> )).to.equal false
