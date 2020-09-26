import React from "react"
import styled from "styled-components"

const StyledDiv = styled.div`
`

const ManuscriptCell = ({ row }) => {
  const { post_title, publication_authors, publication_editors, publication_url, publication_file } = row.original

  const title_link = publication_file ? publication_file : publication_url

  return (
    <StyledDiv>
      <div className="title">
        { title_link
          ? <a href={title_link} target="_blank" dangerouslySetInnerHTML={{__html: post_title}} />
          : <span dangerouslySetInnerHTML={{__html: post_title}} />
        }
      </div>
      { publication_authors
        ? <div className="authors"><span className="label">Authors: </span>
          {
            publication_authors.map(({post_title, permalink}, i, arr) => (
              <><a href={permalink} dangerouslySetInnerHTML={{__html: post_title}} />{ i + 1 < arr.length ? ', ' : ''}</>
            ))
          }
        </div>
        : ''
      }
      { publication_editors
        ? <div className="editors"><span className="label">Editors: </span>
          {
            publication_editors.map(({post_title, permalink}, i, arr) => (
              <><a href={permalink} dangerouslySetInnerHTML={{__html: post_title}} />{ i + 1 < arr.length ? ', ' : ''}</>
            ))
          }
        </div>
        : ''
      }
    </StyledDiv>
  )
}

export default ManuscriptCell