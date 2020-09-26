import React from "react"

import Table from './Table'

import styled from "styled-components"

const StyledDiv = styled.div`
  padding-bottom: 23px;
  .l-section-h {
    max-width: inherit;
  }
`

const Main = ({ items }) => {
  return (
    <StyledDiv className="l-main">
      <div className="l-main-h i-cf">
        <main className="l-content" itemProp="mainContentOfPage">
          <section className="l-section">
            <div className="l-section-h i-cf">
              <Table data={items} />
            </div>
          </section>
        </main>
      </div>
    </StyledDiv>
  )
}

export default Main