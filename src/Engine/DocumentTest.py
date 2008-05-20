import splitter

s = splitter.createScore("""
  <section name="hmi" title="Human/Machine Interface and other features">
    <element name="skins" title="Customizable skins support">
      <desc0>No customizable skin</desc0>
      <desc1>Custumizable skin but needs complicated manipulation</desc1>
      <desc2>User friendly skin support</desc2>
      <score></score>
      <comment>List of other clients using the same skin constituent:
            </comment>
    </element>
  </section>""")
print s