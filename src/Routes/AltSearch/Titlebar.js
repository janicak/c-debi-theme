import React from "react"
import styled from "styled-components"

const StyledDiv = styled.div`
    padding: 22px 40px 14px;
    .l-titlebar-h {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
    }
`

const Titlebar = ({ title }) => {
  return (
    <StyledDiv className="l-titlebar size_medium color_alternate">
      <div className="l-titlebar-h">
        <div className="l-titlebar-content">
          <h1 itemProp="headline">{title}</h1>
        </div>
      </div>
    </StyledDiv>
  )
}

export default Titlebar