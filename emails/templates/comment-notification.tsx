import {
  Body,
  Button,
  Column,
  Container,
  Head,
  Html,
  Img,
  Link,
  Preview,
  Row,
  Section,
  Text,
} from "@react-email/components";
import { If } from "./components/If";

const logoUrl = "https://realtroll.de/assets/img/icons/favicon-48.png";

const fontFamily =
  "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";

export const CommentNotificationEmailProps = [
  "name",
  "articleTitle",
  "time",
  "preview",
  "panelUrl",
  "articleUrl",
  "parentName",
  "parentExcerpt",
] as const;

type Props = Record<(typeof CommentNotificationEmailProps)[number], string>;

export function CommentNotificationEmail({
  name,
  articleTitle,
  time,
  preview,
  panelUrl,
  articleUrl,
  parentName,
  parentExcerpt,
}: Props) {
  return (
    <Html lang="de">
      <Head />
      {/* Preheader: the inbox line previews the actual comment, not the subject. */}
      <Preview>{preview}</Preview>
      <Body style={{ margin: 0, backgroundColor: "#ffffff" }}>
        <Container
          style={{
            maxWidth: "520px",
            margin: "0 auto",
            padding: "24px",
            fontFamily,
            color: "#1c1917",
          }}
        >
          <Row>
            <Column style={{ width: "28px", verticalAlign: "middle" }}>
              <Img
                src={logoUrl}
                width={24}
                height={24}
                alt=""
                style={{ display: "block" }}
              />
            </Column>
            <Column style={{ verticalAlign: "middle", paddingLeft: "8px" }}>
              <Text
                style={{
                  margin: 0,
                  fontSize: "13px",
                  letterSpacing: "0.02em",
                  color: "#57534d",
                }}
              >
                realtroll.de
              </Text>
            </Column>
          </Row>

          <Text
            style={{
              marginTop: "24px",
              marginBottom: "2px",
              fontSize: "16px",
              fontWeight: 600,
            }}
          >
            Neuer Kommentar von {name}
          </Text>
          <Text style={{ margin: 0, fontSize: "14px", color: "#57534d" }}>
            zu „{articleTitle}“ · {time} Uhr
          </Text>

          <If name="parentName" value={parentName}>
            <Section
              style={{
                marginTop: "16px",
                paddingLeft: "14px",
                borderLeft: "3px solid #e7e5e4",
              }}
            >
              <Text style={{ margin: 0, fontSize: "13px", color: "#57534d" }}>
                Antwort auf {parentName}
              </Text>
              <Text
                style={{
                  marginTop: "2px",
                  marginBottom: 0,
                  fontSize: "13px",
                  lineHeight: "1.5",
                  color: "#57534d",
                }}
              >
                {parentExcerpt}
              </Text>
            </Section>
          </If>

          <Section
            style={{
              marginTop: "16px",
              paddingLeft: "14px",
              borderLeft: "3px solid #bd3900",
            }}
          >
            <Text
              style={{
                margin: 0,
                fontSize: "15px",
                lineHeight: "1.55",
                whiteSpace: "pre-wrap",
                wordBreak: "break-word",
              }}
            >
              {preview}
            </Text>
          </Section>

          {/* Article first: comments auto-publish, so replying in context is the
              common act and moderation the exception. The button's border and fill
              degrade to a plain colored link in Outlook desktop – intended. */}
          <Section style={{ marginTop: "24px" }}>
            <Button
              href={articleUrl}
              style={{
                padding: "10px 16px",
                backgroundColor: "#bd3900",
                border: "2px solid #9a2e00",
                borderRadius: "4px",
                color: "#ffffff",
                fontSize: "15px",
                fontWeight: 600,
                lineHeight: "1",
                display: "inline-block",
                textDecoration: "none",
                textAlign: "center",
                verticalAlign: "middle",
              }}
            >
              Im Artikel ansehen
            </Button>
            {/* Keep separator and link glued: on a narrow wrap they drop to a
                second line together instead of orphaning the middot. */}
            <span style={{ whiteSpace: "nowrap", verticalAlign: "middle" }}>
              <span
                aria-hidden="true"
                style={{ padding: "0 10px", fontSize: "14px", color: "#a6a09b" }}
              >
                ·
              </span>
              <Link
                href={panelUrl}
                style={{
                  fontSize: "14px",
                  color: "#57534d",
                  textDecorationLine: "underline",
                }}
              >
                Im Panel moderieren
              </Link>
            </span>
          </Section>
        </Container>
      </Body>
    </Html>
  );
}

CommentNotificationEmail.PreviewProps = {
  name: "Dat-Sarab",
  articleTitle: "Nachbarlicht: die neue Demo",
  time: "05.07.2026 14:32",
  preview:
    "Ich hänge gerade am Gerätepuzzle im Maschinenraum der Amaltheia – die Bauteile lassen sich partout nicht in die richtige Reihenfolge schieben. Übersehe ich einen Hinweis, oder braucht es einen bestimmten Kniff? Ansonsten: Der kompakte Einstieg zieht einen sofort rein, großartig!",
  panelUrl: "https://realtroll.de/panel/pages/blog+nachbarlicht-demo",
  articleUrl: "https://realtroll.de/blog/nachbarlicht-demo#kommentar-7f3a2c",
  parentName: "Kelven",
  parentExcerpt:
    "Die Abstimmung zwischen Geschichte und Spielmechanik ist dir wieder sehr gelungen – besonders die Schiebeaufgaben, um etwas zusammenzubauen.",
} satisfies Props;

export default CommentNotificationEmail;
