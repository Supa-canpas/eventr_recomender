import requests
from bs4 import BeautifulSoup

def getUrl():
    url = 'https://collabo-cafe.com/'
    res = requests.get(url)
    soup = BeautifulSoup(res.text, 'html.parser')
    allUrldiv = soup.find('div','widget__post__list')
    allUrla = allUrldiv.find_all('a')
    infoUrl =[]
    for i in allUrla:
        buf = i["href"]
        infoUrl.append(buf)
    print(infoUrl)


def getInfo(url):
    # url = 'https://collabo-cafe.com/events/collabo/shingeki-gigo-cafe-ikebukuro2024/'
    res = requests.get(url)
    soup = BeautifulSoup(res.text, 'html.parser')
    title = soup.find('h1').text
    news = soup.select_one('#main > article > section.entry-content > div.table__container > table')
    records = news.find_all('tr')
    info = []
    info.append(title)
    for i in range(1,4):
        if i == 3:
            buf = records[i].find('a')['href']
            info.append(buf)
        else:
            buf = records[i].find('td').text
            info.append(buf)
    return info

if __name__ == '__main__':
    # url = "https://collabo-cafe.com/events/collabo/shingeki-gigo-cafe-ikebukuro2024/"
    # info = getInfo(url)
    # print(info)
    getUrl()